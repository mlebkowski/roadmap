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

	public function getRecentProjects()
	{
		$query = $this->create();
		$query->filterByState('finished');
		$query->limit(3);
		$query->orderByCreatedAt(Criteria::ASC);
		return $query->find();
	}


} // ProjectQuery
