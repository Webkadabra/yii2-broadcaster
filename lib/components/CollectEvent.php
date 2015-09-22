<?php
namespace canis\broadcaster\components;

use canis\deferred\models\DeferredAction;
use Yii;
use yii\base\Application;

class CollectEvent extends \yii\base\Event
{
	public $module;
}