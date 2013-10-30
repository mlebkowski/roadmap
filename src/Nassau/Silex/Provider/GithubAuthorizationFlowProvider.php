<?php

namespace Nassau\Silex\Provider;

use Nassau\GitHub\AuthorizationFlow;
use Silex\Application;
use Silex\ServiceProviderInterface;

class GithubAuthorizationFlowProvider implements ServiceProviderInterface
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
		$app['github.auth'] = $app->share(function (Application $app)
		{
			$secret = getenv('GITHUB_CLIENT_SECRET') ?: $app['github.client-secret'];
			$clientId = getenv('GITHUB_CLIENT_ID') ?: $app['github.client-id'];
			return new AuthorizationFlow($clientId, $secret, $app['session']);
		});
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
		$app->before([$app['github.auth'], 'onBeforeRequest'], Application::LATE_EVENT/4);
	}

}
