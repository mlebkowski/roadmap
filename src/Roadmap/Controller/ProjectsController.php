<?php

namespace Roadmap\Controller;

use Finite\Factory\PimpleFactory;
use Nassau\GitHub\AuthorizationFlow;
use Propel\Runtime\Collection\Collection;
use Roadmap\FSM\Project as FSMProject;
use Roadmap\Model\Account;
use Roadmap\Model\Project;
use Roadmap\Model\ProjectActivity;
use Roadmap\Model\ProjectQuery;
use Roadmap\Model\User;
use Roadmap\User\AccountManager;
use Roadmap\User\AuthorizationAwareInterface;
use Roadmap\User\UserManager;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ProjectsController implements AuthorizationAwareInterface
{
	/**
	 * @var PimpleFactory
	 */
	private $factory;

	/**
	 * @var Account
	 */
	private $account;

	/**
	 * @var User
	 */
	private $user;

	public function __construct(PimpleFactory $factory)
	{
		$this->factory = $factory;
	}

	public function setAccount(Account $account = null)
	{
		$this->account = $account;
	}

	public function setUser(User $user = null)
	{
		$this->user = $user;
	}

	public function mainAction(Request $request, Application $app)
	{
		$recent = ProjectQuery::create()->getRecentProjects($this->account);
		$ongoing = ProjectQuery::create()->getOngoingProjects($this->account);
		$new = ProjectQuery::create()->getNewProjects($this->account);


		return [
			'projects.page.html.twig',
			'recent' => $this->attachStateMachine($recent->getResults(), 0, 3),
			'finished' => $this->attachStateMachine($recent->getResults(), 1),
			'hasArchive' => $recent->haveToPaginate(),
			'ongoing' => $this->attachStateMachine($ongoing),
			'new' => $this->attachStateMachine($new),
		];
	}

	public function addProjectForm(Request $request, $data = [])
	{
		return [
			'add-project.page.html.twig'
		] + $data;
	}

	public function saveProject(Request $request)
	{
		if (!$request->request->get('title'))
		{
			return $this->addProjectForm($request, $request->request->all());
		}

		$project = new Project;
		$project->setAccount($this->account);
		$project->addUser($this->user);
		$project->setTitle($request->request->get('title'));
		$activity = new ProjectActivity;

		$activity->setActivityType(ProjectActivity::ACTIVITY_CREATE);
		$activity->setUser($this->user);

		$project->addProjectActivity($activity);

		$project->save();
		return new RedirectResponse('/');
	}

	public function assignToProject($slug, $assign)
	{
		$projects = ProjectQuery::create()
			->filterByAccount($this->account)
			->filterBySlug($slug)
			->find();

		if (0 === $projects->count())
		{
			return "";
		}

		/** @var Project $project */
		list ($project) = $this->attachStateMachine($projects);
		if (false === $project->getStateMachine()->canAssign())
		{
			return "";
		}

		if ($assign && false === $project->getUsers()->contains($this->user))
		{
			$activity = new ProjectActivity;
			$activity->setUser($this->user);
			$activity->setActivityType(ProjectActivity::ACTIVITY_ASSIGN);

			$project->addProjectActivity($activity);
			$project->addUser($this->user);
			$project->save();

		}
		elseif (!$assign && $project->getUsers()->contains($this->user))
		{
			$activity = new ProjectActivity;
			$activity->setUser($this->user);
			$activity->setActivityType(ProjectActivity::ACTIVITY_RESIGN);

			$project->addProjectActivity($activity);
			$project->removeUser($this->user);
			$project->save();
		}

		return [
			'blocks/assign-yourself.twig',
			'project' => $project,
			'user' => $this->user,
			'force' => true,
		];
	}



	/**
	 * @param \Propel\Runtime\Collection\Collection $projectList
	 * @param int $offset
	 * @param int $length
	 * @return array
	 */
	private function attachStateMachine(Collection $projectList, $offset = null, $length = null)
	{
		$factory = $this->factory;
		$projectList = array_map(function (Project $project) use ($factory) {
			$stateMachine = $factory->get($project);
			$project->setStateMachine(new FSMProject($stateMachine));
			return $project;

		}, array_slice($projectList->getArrayCopy(), $offset, $length));
		return $projectList;
	}
}