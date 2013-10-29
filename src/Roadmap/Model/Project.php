<?php

namespace Roadmap\Model;

use Finite\StatefulInterface;
use Propel\Runtime\Connection\ConnectionInterface;
use Roadmap\Model\Base\Project as BaseProject;
use Roadmap\FSM\Project as FSMProject;

class Project extends BaseProject implements StatefulInterface
{
	/** @var \Roadmap\FSM\Project */
	private $stateMachine;

	/**
	 * @param \Roadmap\FSM\Project $stateMachine
	 */
	public function setStateMachine(FSMProject $stateMachine)
	{
		$this->stateMachine = $stateMachine;
	}

	public function getDate()
	{
		switch ($this->stateMachine->getCurrentState()->getName())
		{
			case FSMProject::STATE_ABORTED:
			case FSMProject::STATE_FINISHED:
			case FSMProject::STATE_IN_PROGRESS:
			case FSMProject::STATE_PLANNED:
			case FSMProject::STATE_SHELVED:
				return $this->getCreatedAt();
				return $this->getProjectActivities()->getLast()->getCreatedAt();
			case FSMProject::STATE_NEW:
			default:
				return $this->getCreatedAt();
		}
	}

	/**
	 * @return \Roadmap\FSM\Project
	 */
	public function getStateMachine()
	{
		return $this->stateMachine;
	}

	/**
	 * @param string $finiteState
	 */
	public function setFiniteState($finiteState)
	{
		$this->setState($finiteState);
	}

	/**
	 * @return string
	 */
	public function getFiniteState()
	{
		return $this->getState();
	}

}
