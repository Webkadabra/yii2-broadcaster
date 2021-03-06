<?php
namespace canis\broadcaster\handlers;

use Yii;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\broadcaster\models\BroadcastEvent;
use canis\broadcaster\models\BroadcastEventType;
use canis\broadcaster\models\BroadcastSubscription;
use canis\broadcaster\models\BroadcastHandler;

abstract class Handler extends \yii\base\Component implements HandlerInterface
{
	protected $_model;
	protected $_systemId;
    public $lastError;

	abstract public function getName();
    abstract public function isAvailable();

	public function getModel()
	{
		if (!isset($this->_model)) {
			$attributes = ['system_id' => $this->systemId];
			$this->_model = BroadcastHandler::find()->where($attributes)->one();
			if (!$this->_model) {
				$this->_model = new BroadcastHandler;
				$attributes['name'] = $this->getName();
				$this->_model->attributes = $attributes;
				if (!$this->_model->save()) {
					throw new \Exception("Broadcast handler could not be initialized!");
				}
			}
		}
		return $this->_model;
	}

	public function getEvent(BroadcastEventDeferred $item)
    {
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = BroadcastEvent::get($item->broadcast_event_id))) {
            $item->fail("Event is invalid");
            return false;
        }
        return $event;
    }

	public function getEventPayload(BroadcastEventDeferred $item)
    {
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = $this->getEvent($item))) {
            $item->fail("Event is invalid");
            return false;
        }
        return $event->payloadObject;
    }

    public function getEventTypeModel(BroadcastEventDeferred $item)
    {
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = BroadcastEvent::get($item->broadcast_event_id))) {
            $item->fail("Event is invalid");
            return false;
        }
        if (!($eventType = BroadcastEventType::get($event->broadcast_event_type_id))) {
            $item->fail("Event type is invalid");
            return false;
        }
        return $eventType;
    }

    public function getEventType(BroadcastEventDeferred $item)
    {
    	if (!($eventTypeModel = $this->getEventTypeModel($item))) {
    		return false;
    	}
        $broadcaster = Yii::$app->getModule('broadcaster');
        return $broadcaster->getEventType($eventTypeModel->system_id);
    }

    public function getConfiguration(BroadcastSubscription $subscription)
    {
        if (!($configuration = $subscription->configObject)) {
            $item->fail("Configuration object is invalid");
            return false;
        }
        return $configuration;
    }

    public function getSubscriptionModel(BroadcastEventDeferred $item)
    {
        $result = $item->resultObject;
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = BroadcastEvent::get($item->broadcast_event_id))) {
            $item->fail("Event is invalid");
            return false;
        }
        if (!($subscriptionModel = BroadcastSubscription::get($item->broadcast_subscription_id))) {
            $item->fail("Subscription model is invalid");
            return false;
        }
        return $subscriptionModel;
    }


    public function getUser(BroadcastSubscription $subscription)
    {
        $userClass = Yii::$app->classes['User'];
        if (!($user = $userClass::get($subscription->user_id))) {
            return false;
        }
        return $user;
    }

    public function getAllowMinimumPriorityFilter()
    {
        $broadcaster = Yii::$app->getModule('broadcaster');
        return $broadcaster->allowMinimumPriorityFilter;
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