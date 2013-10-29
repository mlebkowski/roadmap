<?php

namespace Roadmap\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Roadmap\Model\Base\ProjectQuery as BaseProjectQuery;
use Roadmap\Model\Map\ProjectTableMap;


/**
 * Skeleton subclass for performing query and update operations on the 'project' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ProjectQuery extends BaseProjectQuery
{

	public function getRecentProjects(Account $account)
	{
		$query = $this->create();
		$query->filterByState('finished');
		$query->orderByCreatedAt(Criteria::DESC);
		$query->filterByAccount($account);
		return $query->paginate(1, 10);
	}

	public function getOngoingProjects(Account $account)
	{
		$query = $this->create();
		$query->filterByState(['in-progress', 'planned']);
		$query->orderByCreatedAt(Criteria::DESC);
		$query->filterByAccount($account);
		return $query->find();
	}

	public function getNewProjects(Account $account)
	{
		$query = $this->create();
		$query->filterByState('new');
		$query->orderByCreatedAt(Criteria::DESC);
		$query->filterByAccount($account);
		return $query->find();
	}


} // ProjectQuery
