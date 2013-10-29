<?php

namespace Roadmap\User;

use Nassau\GitHub\AuthorizationFlow;
use Roadmap\Model\Account;
use Roadmap\Model\Base\AccountQuery;
use Roadmap\Model\User;
use Roadmap\Model\UserQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserManager
{
	const KEY_USER = 'user';

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var string
	 */
	private $accessToken;
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}


	public function onBeforeRequest(Request $request)
	{
		$authorization = $request->attributes->get(AuthorizationFlow::KEY_AUTHORIZATION);
		if (null === $authorization)
		{
			return;
		}
		$this->accessToken = $authorization['access_token'];

		$this->user = $this->session->get(self::KEY_USER);
		if (null === $this->user)
		{
			$user = $this->prepareUser();
			$this->prepareAccounts();

			$this->session->set(self::KEY_USER, $user);
		}

		$request->attributes->set(self::KEY_USER, $this->user);

	}

	private function fetch($path)
	{
		$url = 'https://api.github.com/' . trim($path, '/') . '?access_token=' . $this->accessToken;
		return json_decode(file_get_contents($url), true);
	}

	private function prepareUser()
	{
		$ghUser = $this->fetch('user');

		$user = UserQuery::create()->findOneByLogin($ghUser['login']);
		if (null === $user)
		{
			$user = new User;
			$user->setLogin($ghUser['login']);
			$user->setName($ghUser['name']);
			$user->setGravatarHash($ghUser['gravatar_id']);
			$user->save();
		}

		return $this->user = $user;
	}

	private function prepareAccounts()
	{
		$user = $this->user;
		if (0 === $user->getAccounts()->count())
		{
			array_map(function ($org) use ($user)
			{
				$account = AccountQuery::create()->findOneByName($org['login']);
				if (null === $account)
				{
					$account = new Account;
					$account->setName($org['login']);
					$account->setAvatarUrl($org['avatar_url']);
				}
				$user->addAccount($account);

			}, $this->fetch('user/orgs'));

			$user->save();
		}
	}
}