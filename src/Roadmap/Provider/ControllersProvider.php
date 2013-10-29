<?php

namespace Roadmap\Provider;

use ArrayObject;
use Roadmap\Controller\ProjectsController;
use Roadmap\User\AccountManager;
use Roadmap\User\UserManager;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

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
		$controllers->get('/add-project', 'controller.projects:addProjectForm');
		$controllers->post('/add-project', 'controller.projects:saveProject');

		$controllers->post('/project/{slug}/{assign}', 'controller.projects:assignToProject')
			->assert('assing', '/^(assing|resign)$/')
			->convert('assing', function ($value) {
				return $value === 'assign';
			});


		/** @noinspection PhpUndefinedMethodInspection */
		$controllers->value('github.scope', $app['github.scope']);
		/** @noinspection PhpUndefinedMethodInspection */
		$controllers->before(function (Request $request) use ($app)
		{
			/** @var EventDispatcher $dispatcher */
			$dispatcher = $app['dispatcher'];
			$dispatcher->addListener(KernelEvents::VIEW, function (GetResponseForControllerResultEvent $e) use ($app)
			{
				if (false === is_array($e->getControllerResult()))
				{
					return;
				}

				/** @var \Twig_Environment $twig */
				$twig = $app['twig'];
				$context = $e->getControllerResult();
				$template = array_shift($context);

				$response = new Response($twig->render($template, [
					'account' => $e->getRequest()->attributes->get(AccountManager::KEY_ACCOUNT),
					'user' => $e->getRequest()->attributes->get(UserManager::KEY_USER),
				] + $context));
				$e->setResponse($response);
			});
		});



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
			return new ProjectsController($app['fsm.factory']);
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
		/** @var ArrayObject $collection */
		$collection = $app['authorization-aware'];
		$collection->append('controller.projects');
	}


}