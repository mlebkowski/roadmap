<?php

namespace Roadmap\Provider;

use ArrayObject;
use Nassau\Silex\HttpKernel\TemplateResponse;
use Roadmap\Controller\ProjectsController;
use Roadmap\Controller\UsersController;
use Roadmap\User\AccountManager;
use Roadmap\User\UserManager;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
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

		$controllers->get('/project/{slug}', 'controller.projects:projectDetails');
		$controllers->post('/project/{slug}', 'controller.projects:transitionProject');

		$controllers->post('/project/{slug}/{assign}', 'controller.projects:assignToProject')
			->assert('assing', '/^(assing|resign)$/')
			->convert('assing', function ($value) {
				return $value === 'assign';
			});

		$controllers->get('/team', 'controller.users:showTeamList');
		$controllers->get('/you', 'controller.users:showProfileAction');
		$controllers->get('/user/{login}/{version}', 'controller.users:showProfileAction')->value('version', null);

		$controllers->post('/you', 'controller.users:saveV2mom');


		/** @var EventDispatcher $dispatcher */
		$dispatcher = $app['dispatcher'];

		/** @noinspection PhpUndefinedMethodInspection */
		$controllers->value('github.scope', $app['github.scope']);
		/** @noinspection PhpUndefinedMethodInspection */
		$controllers->before(function (Request $request) use ($app, $dispatcher)
		{
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
		$dispatcher->addListener(KernelEvents::RESPONSE, function (FilterResponseEvent $e) use ($app)
		{
			$response = $e->getResponse();
			if ($response instanceof TemplateResponse)
			{
				$response->render($app['twig'], [
					'account' => $e->getRequest()->attributes->get(AccountManager::KEY_ACCOUNT),
					'user' => $e->getRequest()->attributes->get(UserManager::KEY_USER),
				]);
			}
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
		$app['controller.users'] = $app->share(function (Application $app)
		{
			return new UsersController;
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
		$collection->append('controller.users');
	}


}