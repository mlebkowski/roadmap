<?php

namespace Roadmap\Model;

use Roadmap\Model\Base\ProjectActivity as BaseProjectActivity;

class ProjectActivity extends BaseProjectActivity
{
	const ACTIVITY_CREATE = 'create';
	const ACTIVITY_EDIT = 'edit';
	const ACTIVITY_RESEARCH = 'research';
	const ACTIVITY_START = 'start';
	const ACTIVITY_DELAY = 'delay';
	const ACTIVITY_DEPLOY = 'deploy';
	const ACTIVITY_RESTORE = 'restore';
	const ACTIVITY_CANCEL = 'cancel';

	const ACTIVITY_ASSIGN = 'assign';
	const ACTIVITY_RESIGN = 'resing';

}
