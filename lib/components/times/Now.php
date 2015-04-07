<?php
namespace canis\notification\components\times;

use Yii;
use canis\notification\components\Notification;

class Now extends Time
{
	public function getScheduledTime(Notification $notification)
	{
		return time();
	}

}