<?php
namespace canis\broadcaster\eventTypes;
use canis\broadcaster\models\BroadcastEvent;
use canis\broadcaster\models\BroadcastEventType;

abstract class EventType extends \yii\base\Component implements EventTypeInterface
{
	const PRIORITY_LOW = 'low';
	const PRIORITY_MEDIUM = 'medium';
	const PRIORITY_HIGH = 'high';
	const PRIORITY_CRITICAL = 'critical';

	protected $_model;
	protected $_systemId;
	protected $_priority;

	abstract public function getName();
	
	public function getPriority()
	{
		if ($this->_priority === null) {
			return static::PRIORITY_MEDIUM;
		}
		return $this->_priority;
	}

	public function setPriority($priority)
	{
		$this->_priority = $priority;
	}
	
	public function triggerBroadcastEvent($payload, $objectId = null, $priority = null)
	{
		if (is_null($priority)) {
			$priority = $this->priority;
		}
		if (is_object($objectId)) {
			$objectId = $objectId->primaryKey;
		}
		$model = new BroadcastEvent;
		$model->broadcast_event_type_id = $this->model->primaryKey;
		$model->priority = $priority;
		$model->object_id = $objectId;
		$model->payloadObject = $payload;
		if (!$model->save()) {
			return false;
		}
		return true;
	}

	public function getModel()
	{
		if (!isset($this->_model)) {
			$attributes = ['system_id' => $this->systemId];
			$this->_model = BroadcastEventType::find()->where($attributes)->one();
			if (!$this->_model) {
				$this->_model = new BroadcastEventType;
				$attributes['name'] = $this->getName();
				$this->_model->attributes = $attributes;
				if (!$this->_model->save()) {
					throw new \Exception("Broadcast handler could not be initialized!");
				}
			}
		}
		return $this->_model;
	}

	public function getSystemId()
	{
		return $this->_systemId;
	}

	public function setSystemId($systemId)
	{
		$this->_systemId = $systemId;
	}
}