<?php

namespace Roadmap\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Roadmap\Model\Base\User as BaseUser;
use Roadmap\Model\Base\UserV2momQuery;
use Roadmap\Model\Map\AccountTableMap;

class User extends BaseUser
{
	protected $v2mom;

	public function getV2mom(Account $account)
	{
		return $this->v2mom
			?: $this->v2mom = UserV2momQuery::create()->filterByAccount($account)->filterByUser($this)->findOne()
			?: $this->v2mom = $this->createV2mom($account);
	}

	public function getGravatar($size = 64)
	{
		$query = http_build_query([
			's' => $size,
			'd' => $this->getPicture()
		]);
		return 'http://www.gravatar.com/avatar/' . $this->getGravatarHash() . '?' . $query;
	}

	private function createV2mom(Account $account)
	{
		$v2mom = new UserV2mom;
		$v2mom->setUser($this);
		$v2mom->setAccount($account);
		return $v2mom;
	}
}
