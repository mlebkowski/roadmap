<?php

namespace Roadmap\Controller;

use Roadmap\Model\User;
use Roadmap\Model\UserQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends AuthorizationAwareController
{
	public function showTeamList()
	{
		$users = UserQuery::create()->filterByAccount($this->getAccount())->find();
		return [
			'team.page.twig',
			'users' => $users,
		];
	}
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


		$v2mom = $profile->getV2mom($this->getAccount());
		$version = $version ?: $v2mom->getLastVersionNumber();
		$isLast = $v2mom->getLastVersionNumber() == (int) $version;
		$v2momVersion = $v2mom->getOneVersion($version);


		return [
			'user.page.twig',
			'profile' => $profile,
			'v2mom' => $v2momVersion,
			'isLast' => $isLast,
		];
	}

	public function saveV2mom(Request $request)
	{
		$v2mom = $this->getUser()->getV2mom($this->getAccount());
		$v2mom->setVision($request->request->get('vision'));
		$v2mom->setValues($request->request->get('values'));
		$v2mom->setMethods($request->request->get('methods'));
		$v2mom->setObstacles($request->request->get('obstacles'));
		$v2mom->save();

		return new RedirectResponse('/you');
	}
}