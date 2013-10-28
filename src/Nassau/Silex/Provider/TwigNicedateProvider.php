<?php


namespace Nassau\Silex\Provider;


use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Translation\Translator;

class TwigNicedateProvider implements ServiceProviderInterface
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
		$app['twig.filter.nicedate'] = $app->share(function (Application $app)
		{
			return new \Twig_SimpleFilter('nicedate', function (\DateTime $date) use ($app)
			{
				/** @var Translator $translator */
				$translator = $app['translator'];

				return $translator->trans('nicedate', [
					'%day%' => $date->format('j'),
					'%month%' => $translator->trans('months.' . ($date->format('m')-1)),
					'%year%' => $date->format('Y'),
				]);

			});
		});
	}

	/**
	 * Bootstraps the application.
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app)
	{
		$app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig, Application $app)
		{
			$twig->addFilter($app['twig.filter.nicedate']);
			return $twig;
		}));
	}

}