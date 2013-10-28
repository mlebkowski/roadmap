<?php

namespace Roadmap\Provider;

use Roadmap\Controller\ProjectsController;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;

class ControllersProvider implements ServiceProviderInterface, ControllerProviderInterface
{
	/**
	 * Returns routes to connect to the given application.
	 *
	 * @param Application $app An Application instance
	 *
	 * @return ControllerCollection A ControllerCollection instance
	 */
	public function connect(Application $app)
	{
		/** @var ControllerCollection $controllers */
		$controllers = $app['controllers_factory'];
		$controllers->get('/', 'controller.projects:mainAction');

		return $controllers;
	}

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
		$app['controller.projects'] = $app->share(function (Application $app)
		{
			return new ProjectsController($app['twig']);
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
		// noop
	}


}