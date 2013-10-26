<?php

namespace Nassau\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class TranslationLoaderProvider implements ServiceProviderInterface
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
		$app->before(function (Request $request) use ($app)
		{
			$locales = $app['nassau.translation.languages'];
			$request->attributes->set('_locale', $request->getPreferredLanguage($locales));
		}, Application::EARLY_EVENT);
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
		if (false === $app->offsetExists('nassau.translation.languages'))
		{
			$app['nassau.translation.languages'] = array_map(function ($filename)
				{
					return basename($filename, '.yaml');
				},
				glob($app['translator.loader.path'] . '/*.yaml'));
		}

		$app['translator'] = $app->share($app->extend('translator', function (Translator $translator, Application $app)
		{
			$translator->addLoader('yaml', new YamlFileLoader);

			foreach ($app['nassau.translation.languages'] as $lang)
			{
				$translator->addResource('yaml', $app['translator.loader.path'] . '/' . $lang . '.yaml', $lang);
			}

			return $translator;
		}));
	}
}