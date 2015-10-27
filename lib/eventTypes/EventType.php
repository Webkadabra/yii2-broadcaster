<?php
namespace canis\broadcaster\eventTypes;
use Yii;
use canis\helpers\StringHelper;
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

	abstract public function getBatchable();
	
	public function getSchedule(BroadcastEvent $event)
	{
		return null;
	}

	public function getDescriptor(BroadcastEvent $event)
	{
		if (!($descriptor = $this->getDescriptorString())) {
			return false;
		}
		return StringHelper::simpleTwig($descriptor, $this->getDescriptorMeta($event));
	}

	public function getDescriptorString()
	{
		return false;
	}

	public function getDescriptorMeta(BroadcastEvent $event)
	{
		$meta = $event->payloadObject->data;
		$meta['_application'] = Yii::$app->name;
		return $meta;
	}


	public function getMeta(BroadcastEvent $event)
	{
		$meta = [];
		$meta['_application'] = Yii::$app->name;
		$meta['id'] = $event->primaryKey;
		$meta['descriptor'] = $this->getDescriptor($event);
		$meta['created'] = strtotime($event->created);
		$meta['created_human'] = date("M d, Y g:i:s A", strtotime($event->created));
		$meta['object'] = null;
		$registryClass = Yii::$app->classes['Registry'];
		if (!empty($event->object_id) && ($object = $registryClass::getObject($event->object_id))) {
			$meta['object'] = ['id' => $object->id, 'descriptor' => $object->descriptor, 'subdescriptor' => $object->subdescriptor];
		}
		return $meta;
	}

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
		foreach ($this->getRequiredPayloadKeys() as $key) {
			if (!isset($payload->data[$key])) {
				return false;
			}
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
	
	public function getRequiredPayloadKeys()
	{
		return [];
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