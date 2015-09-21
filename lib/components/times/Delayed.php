<?php
namespace canis\broadcaster\components\times;

use Yii;
use canis\broadcaster\components\Notification;

class Delayed extends Time
{
	/*
	int Delay in seconds 
	*/
	public $delay = 3600;

	public function getScheduledTime(Notification $notification)
	{
		return time() + $this->delay;
	}
}