<?php
namespace canis\broadcaster\components;
 
use Yii;
use canis\broadcaster\Module;

trait BroadcastableTrait {
	//static public function collectEventTypes();

	public function triggerBroadcastEvent($id, $payload, $objectId = null, $priority = null)
	{
		if (!is_object($payload)) {
			$payload = Yii::createObject(['class' => Payload::className(), 'data' => $payload]);
		}
		if (!($payload instanceof Payload)) {
			return false;
		}
		$eventTypeId = Module::generateEventTypeId($id, get_class($this));
		$eventType = Yii::$app->getModule('broadcaster')->getEventType($eventTypeId);
		if (!$eventType) {
			return false;
		}
		return $eventType->triggerBroadcastEvent($payload, $objectId, $priority);
	}
}