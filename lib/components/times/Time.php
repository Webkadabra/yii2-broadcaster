<?php
namespace canis\notification\components\times;

use Yii;
use canis\notification\components\Notification;

abstract class Time extends \yii\base\Object
{
	abstract public function getScheduledTime(Notification $notification);
}