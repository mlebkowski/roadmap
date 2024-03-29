<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('roadmap', 'mysql');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration(array (
  'dsn' => 'mysql:host=127.0.0.1;dbname=roadmap.dev',
  'user' => 'roadmap.dev',
  'password' => 'roadmap.dev',
  'settings' =>
  array (
    'charset' => 'utf8',
  ),
));
$manager->setName('roadmap');
$serviceContainer->setConnectionManager('roadmap', $manager);
$serviceContainer->setDefaultDatasource('roadmap');