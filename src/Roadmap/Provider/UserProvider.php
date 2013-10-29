<?php

namespace Roadmap\Provider;

use Roadmap\User\AuthorizationAwareInterface;
use Roadmap\User\AccountManager;
use Roadmap\User\UserManager;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class UserProvider implements ServiceProviderInterface
{
	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app)
	{
		$app['user-manager'] = $app->share(function (Application $app)
		{
			return new UserManager($app['session']);
		});
		$app['account-manager'] = $app->share(function (Application $app)
		{
			return new AccountManager($app['domain.base'], $app['user-manager']);
		});

		$app['authorization-aware'] = new \ArrayObject;

	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app)
	{
		$app->before([$app['user-manager'], 'onBeforeRequest'], Application::LATE_EVENT/2);
		$app->before([$app['account-manager'], 'onBeforeRequest'], Application::LATE_EVENT/4*3);

		$app->before(function (Request $request) use ($app)
		{
			$user = $request->attributes->get(UserManager::KEY_USER);
			$account = $request->attributes->get(AccountManager::KEY_ACCOUNT);
			foreach ($app['authorization-aware'] as $serviceId)
			{
				if (false === $app->offsetExists($serviceId))
				{
					continue;
				}
				$app[$serviceId] = $app->share($app->extend($serviceId, function ($service) use ($user, $account)
				{
					if ($service instanceof AuthorizationAwareInterface)
					{
						$service->setAccount($account);
						$service->setUser($user);
					}
					return $service;
				}));
			}
		}, Application::LATE_EVENT);

	}

}