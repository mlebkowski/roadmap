<?php

namespace Roadmap\Controller;

use Finite\Factory\PimpleFactory;
use Roadmap\FSM\Project as FSMProject;
use Roadmap\Model\Project;
use Roadmap\Model\ProjectQuery;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ProjectsController
{
	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	public function __construct(\Twig_Environment $twig)
	{
		$this->twig = $twig;
	}


	public function mainAction(Request $request, Application $app)
	{
		$projectName = 'ZnanyLekarz';

		$projectList = ProjectQuery::create()->getRecentProjects();

		/** @var PimpleFactory $factory */
		$factory = $app['fsm.factory'];
		$projectList = array_map(function (Project $project) use ($factory)
		{
			$stateMachine = $factory->get($project);
			$project->setStateMachine(new FSMProject($stateMachine));
			return $project;

		}, $projectList->getArrayCopy());

		return $this->twig->render('projects.page.html.twig', [
			'recent' => $projectList,
			'projectName' => $projectName
		]);
	}
}