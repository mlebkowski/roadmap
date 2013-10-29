<?php

namespace Nassau\GitHub;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthorizationFlow
{
	const KEY_STATE = 'github.state';
	const KEY_AUTHORIZATION = 'github.authorization';

	/**
	 * @var array
	 */
	private $authorization;
	/**
	 * @var string
	 */
	private $clientSecret;
	/**
	 * @var string
	 */
	private $clientId;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $persistence;

	public function __construct($clientId, $clientSecret, Session $persistence)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->persistence = $persistence;

//		session_start();
		$this->persistence->start();
		$this->authorization = $this->persistence->get(self::KEY_AUTHORIZATION);
	}

	public function onBeforeRequest(Request $request)
	{
		if (null === $request->attributes->get('github.scope'))
		{
			return null;
		}

		$scope = $request->get('github.scope');

		if ($request->query->get('code'))
		{
			$code = $request->query->get('code');
			$state = $request->query->get('state');
			$this->authorization = $this->replaceCodeWithAccessToken($code, $state);
			$this->persistence->set(self::KEY_AUTHORIZATION, $this->authorization);
			return new RedirectResponse('/' . $request->getBaseUrl());
		}

		if (false === $this->isAuthorized())
		{
			$url = $this->getAuthorizationUrl($scope, $request->getUri());
			return new RedirectResponse($url);
		}

		$request->attributes->set(self::KEY_AUTHORIZATION, $this->authorization);
		return null;
	}

	private function isAuthorized()
	{
		return null !== $this->authorization && empty($this->authorization['error']);
	}

	private function getAuthorizationUrl($scope, $redirectUri = "")
	{
		$state = sha1(openssl_random_pseudo_bytes(20));
		$this->persistence->set(self::KEY_STATE, $state);

		return "https://github.com/login/oauth/authorize?" . http_build_query([
			'client_id' => $this->clientId,
			'redirect_uri' => $redirectUri,
			'scope' => $scope,
			'state' => $state,
		]);
	}

	private function replaceCodeWithAccessToken($code, $state)
	{
		if ($this->persistence->get(self::KEY_STATE) !== $state)
		{
			return null;
		}

		$opts = [
			'http' =>
			[
				'method' => 'POST',
				'header' => implode("\r\n", [
					'Content-type: application/x-www-form-urlencoded',
					'Accept: application/json',
				]),
			]
		];
		$context  = stream_context_create($opts);
		$url = "https://github.com/login/oauth/access_token?" . http_build_query([
			'client_id' => $this->clientId,
			'code' => $code,
			'client_secret' => $this->clientSecret,
		]);
		$data = file_get_contents($url, false, $context);
		return json_decode($data, true);
	}


}