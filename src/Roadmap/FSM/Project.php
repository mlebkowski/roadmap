<?php

namespace Roadmap\FSM;

use Finite\StatefulInterface;
use Finite\StateMachine\ListenableStateMachine;
use Roadmap\Model\Project as ProjectModel;

class Project implements StatefulInterface
{
	const STATE_NEW = 'new';
	const STATE_PLANNED = 'planned';
	const STATE_IN_PROGRESS = 'in-progress';
	const STATE_SHELVED = 'shelved';
	const STATE_ABORTED = 'aborted';
	const STATE_FINISHED = 'finished';

	const TRANSITION_RESEARCH = 'research';
	const TRANSITION_START = 'start';
	const TRANSITION_DEPLOY = 'deploy';
	const TRANSITION_CANCEL = 'cancel';
	const TRANSITION_DELAY = 'delay';
	const TRANSITION_RESTORE = 'restore';

	/**
	 * @var \Finite\StateMachine\ListenableStateMachine
	 */
	private $stateMachine;

	/**
	 * @var \Roadmap\Model\Project
	 */
	private $project;

	public function __construct(ListenableStateMachine $stateMachine, ProjectModel $project)
	{
		$this->stateMachine = $stateMachine;
		$this->project = $project;
	}

	/**
	 * @param string $finiteState
	 */
	public function setFiniteState($finiteState)
	{
		$this->project->setState($finiteState);
	}

	/**
	 * @return string
	 */
	public function getFiniteState()
	{
		return $this->project->getState();
	}

	public function canCancel()
	{
		return $this->stateMachine->can(self::TRANSITION_CANCEL);
	}

	public function cancel()
	{
		$this->stateMachine->apply(self::TRANSITION_CANCEL);
		// TODO:
	}

	public function canDeploy()
	{
		return $this->stateMachine->can(self::TRANSITION_DEPLOY);
	}

	public function deploy()
	{
		$this->stateMachine->apply(self::TRANSITION_DEPLOY);
		// TODO:
	}

	public function canResearch()
	{
		return $this->stateMachine->can(self::TRANSITION_RESEARCH);
	}

	public function reserach()
	{
		$this->stateMachine->apply(self::TRANSITION_RESEARCH);
		// TODO:
	}

	public function canStart()
	{
		return $this->stateMachine->can(self::TRANSITION_START);
	}

	public function start()
	{
		$this->stateMachine->apply(self::TRANSITION_START);
		// TODO:
	}

	public function canAssign()
	{
		return false === $this->stateMachine->getCurrentState()->isFinal();
	}

	public function canResign()
	{
		return false === $this->stateMachine->getCurrentState()->isFinal();
	}



}
