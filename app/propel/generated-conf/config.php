<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('roadmap', 'mysql');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration(array (
  'dsn' => 'mysql:host=localhost;dbname=roadmap.dev',
  'user' => 'roadmap.dev',
  'password' => 'roadmap.dev',
));
$manager->setName('roadmap');
$serviceContainer->setConnectionManager('roadmap', $manager);
$serviceContainer->setDefaultDatasource('roadmap');