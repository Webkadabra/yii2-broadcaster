<?php
namespace canis\broadcaster\components\times;

use Yii;
use canis\broadcaster\components\Notification;

class UserPreference extends Time
{
	public function getScheduledTime(Notification $notification)
	{
		return null;
	}
}