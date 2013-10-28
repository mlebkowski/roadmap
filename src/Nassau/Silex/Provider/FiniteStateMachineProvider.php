<?php


namespace Nassau\Silex\Provider;


use Finite\Factory\PimpleFactory;
use Finite\Loader\ArrayLoader;
use Finite\StateMachine\ListenableStateMachine;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class FiniteStateMachineProvider implements ServiceProviderInterface
{
	/**
	 * Registers services on the given app.
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app)
	{
		$app['fsm.factory'] = $app->share(function (Application $app)
		{
			return new PimpleFactory($app, 'fsm.state-machine');
		});

		$app['fsm.state-machine'] = function (Application $app)
		{
			$stateMachine = new ListenableStateMachine;
			$stateMachine->setEventDispatcher($app['dispatcher']);
			return $stateMachine;
		};
	}

	/**
	 * Bootstraps the application.
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app)
	{
		$app['fsm.factory'] = $app->share($app->extend('fsm.factory', function (PimpleFactory $factory, Application $app)
		{
			$finder = (new Finder)->files()->in($app['path.config'] . '/fsm')->name('*.yaml');

			/** @var \SplFileInfo $file */
			foreach ($finder->files() as $file)
			{
				$contents = file_get_contents($file->getRealPath());
				$config = Yaml::parse($contents);
				$loader = new ArrayLoader($config);
				$factory->addLoader($loader);
			}
			return $factory;
		}));
	}

}