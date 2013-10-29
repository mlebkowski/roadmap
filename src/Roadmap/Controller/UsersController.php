<?php

namespace Roadmap\Controller;

use Roadmap\Model\User;
use Roadmap\Model\UserQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends AuthorizationAwareController
{
	public function showProfileAction($login = "", $version = 0)
	{
		if ($login)
		{
			/** @var User $profile */
			$profile = UserQuery::create()
				->filterByAccount($this->getAccount())
				->filterByLogin($login)
				->findOne();
		}
		else
		{
			$profile = $this->getUser();
		}

		if (null === $profile)
		{
			throw new NotFoundHttpException;
		}

		return [
			'user.twig',
			'profile' => $profile,
			'v2mom' =>  $profile->getV2mom($this->getAccount()),
		];
	}
}