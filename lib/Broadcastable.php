<?php
namespace canis\broadcaster;
 
use Yii;
use canis\broadcaster\components\Payload;

class Broadcastable extends \yii\base\Behavior
{
	public function triggerBroadcastEvent($id, $payload, $objectId = null, $priority = null)
	{
		if (!is_object($payload)) {
			$payload = Yii::createObject(['class' => Payload::className(), 'data' => $payload]);
		}
		if (!($payload instanceof Payload)) {
			return false;
		}
		$eventTypeId = Module::generateEventTypeId($id, get_class($this->owner));
		$eventType = Yii::$app->getModule('broadcaster')->getEventType($eventTypeId);
		if (!$eventType) {
			throw new \Exception("{$eventTypeId}");
			return false;
		}
		return $eventType->triggerBroadcastEvent($payload, $objectId, $priority);
	}
}