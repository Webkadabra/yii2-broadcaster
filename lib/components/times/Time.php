<?php
namespace canis\broadcaster\components\times;

use Yii;
use canis\broadcaster\components\Notification;

abstract class Time extends \yii\base\Object
{
	abstract public function getScheduledTime(Notification $notification);
}