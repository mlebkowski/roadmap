<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

include dirname(__DIR__) . '/vendor/autoload.php';

$app = new Silex\Application([
	'path.data' => dirname(__DIR__) . '/data',
	'path.config' => dirname(__DIR__) . '/app/config',
	'path.cache' => dirname(__DIR__) . '/cache',
	'path.src' => dirname(__DIR__) . '/src',
	'path.views' => dirname(__DIR__) . '/app/views',
	'path.translations' => dirname(__DIR__) . '/app/translations',
	'path.assets' => dirname(__DIR__) . '/web/assets',
]);

$app['debug'] = "1" === getenv('DEBUG');


$app->register(new Igorw\Silex\ConfigServiceProvider($app['path.config'] . '/application.yaml'));
$app->register(new Nassau\Silex\Provider\DatabaseProvider);
$app->register(new Silex\Provider\TranslationServiceProvider, [
	'nassau.translation.languages' => ['pl', 'en']
]);
$app->register(new Nassau\Silex\Provider\TranslationLoaderProvider, [
	'translator.loader.path' => $app['path.translations'],
]);
$app->register(new Silex\Provider\TwigServiceProvider, [
	'twig.path' => [$app['path.views'], $app['path.assets']],
	'twig.options' => [
		'cache' => rtrim($app['path.cache'] . '/twig/' . getenv('DEP_VERSION'), '/'),
		'debug' => $app['debug'],
		'strict_variables' => true,
	],
]);

$app->register(new Silex\Provider\ServiceControllerServiceProvider);
$app->register(new Silex\Provider\SessionServiceProvider, [
	'session.storage.handler' => function (Application $app) {
		return new PdoSessionHandler($app['db'], [
			'db_table' => 'session',
			'db_id_col' => 'id',
			'db_data_col' => 'data',
			'db_time_col' => 'created_at',
		]);
	},
]);
$app->register(new Nicl\Silex\MarkdownServiceProvider);

return $app;