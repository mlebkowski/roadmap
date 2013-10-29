<?php

namespace Roadmap\Controller;

use Roadmap\Model\Account;
use Roadmap\Model\User;
use Roadmap\User\AuthorizationAwareInterface;

class AuthorizationAwareController implements AuthorizationAwareInterface
{
	/**
	 * @var Account
	 */
	private $account;
	/**
	 * @var User
	 */
	private $user;

	public function setAccount(Account $account = null)
	{
		$this->account = $account;
	}

	public function setUser(User $user = null)
	{
		$this->user = $user;
	}

	protected function getAccount()
	{
		return $this->account;
	}

	protected function getUser()
	{
		return $this->user;
	}

}