<?php
namespace canis\broadcaster\components\times;

use Yii;
use canis\broadcaster\components\Notification;

class Now extends Time
{
	public function getScheduledTime(Notification $notification)
	{
		return time();
	}

}