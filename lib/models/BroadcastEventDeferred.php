<?php

namespace canis\broadcaster\models;

use Yii;
use canis\broadcaster\components\Result;

/**
 * This is the model class for table "broadcast_event_deferred".
 *
 * @property string $id
 * @property string $broadcast_subscription_id
 * @property string $broadcast_event_id
 * @property string $broadcast_event_batch_id
 * @property resource $result
 * @property integer $handled
 * @property string $scheduled
 * @property string $created
 *
 * @property BroadcastEventBatch $broadcastEventBatch
 * @property BroadcastEvent $broadcastEvent
 * @property BroadcastSubscription $broadcastSubscription
 */
class BroadcastEventDeferred extends \canis\db\ActiveRecord
{
    protected $_dataObject;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'serializeData']);
    }

    /**
     * [[@doctodo method_description:serializeAction]].
     */
    public function serializeData()
    {
        if (isset($this->_dataObject)) {
            try {
                $this->result = serialize($this->_dataObject);
            } catch (\Exception $e) {
                \d($this->_dataObject);
                exit;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_event_deferred';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['broadcast_subscription_id', 'broadcast_event_id'], 'required'],
            [['broadcast_event_id', 'broadcast_event_batch_id'], 'integer'],
            [['result'], 'string'],
            [['scheduled', 'created', 'started', 'completed'], 'safe'],
            [['broadcast_subscription_id'], 'string', 'max' => 36],
            [['broadcast_event_batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastEventBatch::className(), 'targetAttribute' => ['broadcast_event_batch_id' => 'id']],
            [['broadcast_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastEvent::className(), 'targetAttribute' => ['broadcast_event_id' => 'id']],
            [['broadcast_subscription_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastSubscription::className(), 'targetAttribute' => ['broadcast_subscription_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'broadcast_subscription_id' => 'Broadcast Subscription ID',
            'broadcast_event_id' => 'Broadcast Event ID',
            'broadcast_event_batch_id' => 'Broadcast Event Batch ID',
            'result' => 'Result',
            'started' => 'Started',
            'completed' => 'Completed',
            'scheduled' => 'Scheduled',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventBatch()
    {
        return $this->hasOne(BroadcastEventBatch::className(), ['id' => 'broadcast_event_batch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEvent()
    {
        return $this->hasOne(BroadcastEvent::className(), ['id' => 'broadcast_event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastSubscription()
    {
        return $this->hasOne(BroadcastSubscription::className(), ['id' => 'broadcast_subscription_id']);
    }

    public function handle()
    {
        if (!empty($this->started)) {
            return false;
        }
        $result = $this->resultObject;
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = BroadcastEvent::get($this->broadcast_event_id))) {
            $this->fail("Event is invalid");
            return false;
        }
        if (!($subscriptionModel = BroadcastSubscription::get($this->broadcast_subscription_id)) || !$subscriptionModel->isValid()) {
            $this->fail("Subscription model is invalid");
            return false;
        }
        if (!($handlerModel = BroadcastHandler::get($subscriptionModel->broadcast_handler_id))) {
            $this->fail("Handler model is invalid");
            return false;
        }
        if (!($handler = $broadcaster->getHandler($handlerModel->system_id))) {
            $this->fail("Handler is invalid");
            return false;
        }

        if (!$handler->isAvailable()) {
            return true;
        }

        $this->started = gmdate("Y-m-d G:i:s");
        if (!$this->save()) {
            return false;
        }
        
        if (!$handler->handle($this)) {
            $this->fail("Item could not be handled: " . $handler->lastError);
            return false;
        }
        $subscriptionModel->last_triggered = gmdate("Y-m-d G:i:s");
        $subscriptionModel->save();
        return $this->complete();
    }

    public function complete($message = false)
    {
        if (empty($this->started)) {
            $this->started = gmdate("Y-m-d G:i:s");
        }
        $this->completed = gmdate("Y-m-d G:i:s");
        if ($message) {
            $this->resultObject->message = $message;
        }
        return $this->save();
    }

    public function fail($error = false)
    {
        
        $this->started = NULL;
        $this->scheduled = gmdate("Y-m-d G:i:s", strtotime("+10 minutes"));
        $this->resultObject->isValid = false;
        if ($error) {
            $this->resultObject->message = $error;
        }
        return $this->save();
    }

    /**
     * Set action object.
     *
     * @param [[@doctodo param_type:ao]] $ao [[@doctodo param_description:ao]]
     */
    public function setResultObject($do)
    {
        $do->model = $this;
        $this->_dataObject = $do;
    }

    /**
     * Get action object.
     *
     * @return [[@doctodo return_type:getActionObject]] [[@doctodo return_description:getActionObject]]
     */
    public function getResultObject()
    {
        if (!isset($this->_dataObject) && !empty($this->result)) {
            $this->_dataObject = unserialize($this->result);
        } elseif (!isset($this->_dataObject)) {
            $this->_dataObject = new Result;
        }
        $this->_dataObject->model = $this;

        return $this->_dataObject;
    }
}
