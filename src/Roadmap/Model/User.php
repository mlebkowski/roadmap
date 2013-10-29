<?php

namespace Roadmap\Model;

use Roadmap\Model\Base\User as BaseUser;

class User extends BaseUser
{
	public function getGravatar($size = 64)
	{
		$query = http_build_query([
			's' => $size,
			'd' => $this->getPicture()
		]);
		return 'http://www.gravatar.com/avatar/' . $this->getGravatarHash() . '?' . $query;
	}
}
