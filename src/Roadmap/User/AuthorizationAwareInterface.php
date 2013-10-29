<?php

namespace Roadmap\User;

use Roadmap\Model\Account;
use Roadmap\Model\User;

interface AuthorizationAwareInterface
{
	public function setAccount(Account $account = null);

	public function setUser(User $user = null);

}