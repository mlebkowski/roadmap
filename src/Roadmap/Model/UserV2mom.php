<?php

namespace Roadmap\Model;

use Roadmap\Model\Base\UserV2mom as BaseUserV2mom;

class UserV2mom extends BaseUserV2mom
{
	public function isVersioningNecessary($con = null)
	{
		if ($this->updated_at > new \DateTime("-5 day"))
		{
			return false;
		}
		return parent::isVersioningNecessary($con);
	}


}
