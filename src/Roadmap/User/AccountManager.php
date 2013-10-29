<?php

namespace Roadmap\User;

use Propel\Runtime\ActiveQuery\Criteria;
use Roadmap\Model\AccountQuery;
use Roadmap\Model\Map\AccountTableMap;
use Roadmap\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class AccountManager
{
	const KEY_ACCOUNT = 'account';

	private $baseDomain;
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;
	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	public function __construct($baseDomain, \Twig_Environment $twig, Session $session)
	{
		$this->baseDomain = $baseDomain;
		$this->session = $session;
		$this->twig = $twig;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return null|\Symfony\Component\HttpFoundation\Response
	 */
	public function onBeforeRequest(Request $request)
	{
		/** @var User $user */
		$user = $this->session->get('user');
		if (null === $user)
		{
			return null;
		}

		$currentName = $this->getAccountName($request->getHost());
		$account = AccountQuery::create()
			->filterByUser($user)
			->findOneByName($currentName);

		if (null === $account)
		{
			return $this->chooseAccount($user);
		}

		$request->attributes->set(self::KEY_ACCOUNT, $account);
		return null;
	}

	private function getAccountName($host)
	{
		if (substr($host, - strlen($this->baseDomain) === $this->baseDomain))
		{
			$host = substr($host, 0, - strlen($this->baseDomain));
		}

		return strtok($host, '.') ?: null;
	}

	private function getUrlForAccount($name)
	{
		return sprintf('http://%s.%s', strtolower($name), $this->baseDomain);
	}

	/**
	 * @param $user
	 * @return RedirectResponse|Response
	 */
	private function chooseAccount($user)
	{
		if (1 === $user->getAccounts()->count()) {
			$name = $user->getAccounts()->getFirst()->getName();
			$url = $this->getUrlForAccount($name);
			return new RedirectResponse($url);
		}

		return new Response($this->twig->render('choose.page.html.twig', [
			'baseDomain' => $this->baseDomain,
			'choices' => $user->getAccounts(),
		]));
	}


}