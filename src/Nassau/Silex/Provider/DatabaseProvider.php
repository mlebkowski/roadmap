<?php

namespace Nassau\Silex\Provider;

use PDO;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
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
		$app['db'] = $app->share(function (Application $app)
		{
			$dsn = sprintf('sqlite:%s/db.sqlite', $app['path.data']);
			$pdo = new PDO($dsn);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return $pdo;
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
		$app['db'] = $app->share($app->extend('db', function (PDO $db, Application $app)
		{
			foreach ($app['db.tables'] as $name => $columns)
			{
				$db->exec(sprintf('CREATE TABLE IF NOT EXISTS `%s` (%s)', $name, implode(',', $columns)));
			}
			return $db;
		}));
	}
}