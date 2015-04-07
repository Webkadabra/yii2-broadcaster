<?php
namespace canis\notification\components\times;

use Yii;
use canis\notification\components\Notification;

class UserPreference extends Time
{
	public function getScheduledTime(Notification $notification)
	{
		return null;
	}
}