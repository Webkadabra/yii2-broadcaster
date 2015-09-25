<?php

namespace canis\broadcaster\models;

use Yii;
use canis\broadcaster\components\Result;

/**
 * This is the model class for table "broadcast_event_batch".
 *
 * @property string $id
 * @property string $user_id
 * @property string $broadcast_handler_id
 * @property resource $result
 * @property integer $handled
 * @property string $scheduled
 * @property string $created
 *
 * @property BroadcastHandler $broadcastHandler
 * @property User $user
 * @property BroadcastEventDeferred[] $broadcastEventDeferreds
 */
class BroadcastEventBatch extends \canis\db\ActiveRecord
{
    const BATCH_TYPE_HOURLY = 'hourly';
    const BATCH_TYPE_DAILY = 'daily';
    const BATCH_TYPE_WEEKLY = 'weekly';
    const BATCH_TYPE_MONTHLY = 'monthly';

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
        return 'broadcast_event_batch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'broadcast_handler_id'], 'required'],
            [['result'], 'string'],
            [['scheduled', 'created', 'started', 'completed'], 'safe'],
            [['user_id', 'broadcast_handler_id'], 'string', 'max' => 36],
            [['broadcast_handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastHandler::className(), 'targetAttribute' => ['broadcast_handler_id' => 'id']],
            // [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'broadcast_handler_id' => 'Broadcast Handler ID',
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
    public function getBroadcastHandler()
    {
        return $this->hasOne(BroadcastHandler::className(), ['id' => 'broadcast_handler_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventDeferreds()
    {
        return $this->hasMany(BroadcastEventDeferred::className(), ['broadcast_event_batch_id' => 'id']);
    }

    public function handle()
    {
        if (!empty($this->started)) {
            return false;
        }
        $this->started = date("Y-m-d G:i:s");
        if (!$this->save()) {
            return false;
        }
        $result = $this->resultObject;
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($handlerModel = BroadcastHandler::get($this->broadcast_handler_id))) {
            $this->fail("Handler model is invalid");
            return false;
        }
        if (!($handler = $broadcaster->getHandler($handlerModel->system_id))) {
            $this->fail("Handler is invalid");
            return false;
        }
        $deferredItems = BroadcastEventDeferred::find()->where(['and', ['broadcast_event_batch_id' => $this->id], 'started IS NULL'])->all();
        if (empty($deferredItems)) {
            $this->complete("No deferred items were found in this batch");
            return true;
        }
        $result = true;
        if (!($handler instanceof \canis\broadcaster\handlers\BatchableHandlerInterface)) {
            // this shouldn't happen
            foreach ($deferredItems as $deferredItem) {
                if (!$deferredItem->handle()) {
                    $result = false;
                }
            }
            if (!$result) {
                $this->fail("Some deferred items didn't finish");
            } else {
                $this->complete("Batch couldn't be processed, but each deferred was handled individually");
            }
        } else {
            if (!$handler->handleBatch($this, $deferredItems)) {
                $this->fail("Deferred batch could not be handled");
                $result = false;
            } else {
                $this->complete();
            }
            foreach ($deferredItems as $deferredItem) {
                if ($result) {
                    $deferredItem->complete();
                    $subscriptionModel = BroadcastSubscription::get($deferredItem->broadcast_subscription_id);
                    $subscriptionModel->last_triggered = date("Y-m-d G:i:s");
                    $subscriptionModel->save();
                } else {
                    $deferredItem->fail($this->resultObject->message);
                }
            }
        }
        return $result;
    }

    public function complete($message = false)
    {
        if (empty($this->started)) {
            $this->started = date('Y-m-d G:i:s');
        }
        $this->completed = date('Y-m-d G:i:s');
        if ($message) {
            $this->resultObject->message = $message;
        }
        return $this->save();
    }

    public function fail($error = false)
    {
        if (empty($this->started)) {
            $this->started = date('Y-m-d G:i:s');
        }
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

    public static function getBatch($userId, $handlerId, $batchType)
    {
        if (empty($batchType)) {
            return null;
        }
        $endOfWeek = 'Saturday';
        if (class_exists('IntlCalendar')) {
            $cal = \IntlCalendar::createInstance();
            if ($cal->getFirstDayOfWeek() === \IntlCalendar::DOW_MONDAY) {
                $endOfWeek = 'Sunday';
            }
        }
        if ($batchType === static::BATCH_TYPE_HOURLY) {
            $scheduled = date('Y-m-d G:59:59');
        } elseif ($batchType === static::BATCH_TYPE_DAILY) {
            $scheduled = date('Y-m-d') . " 23:59:59";
        } elseif ($batchType === static::BATCH_TYPE_WEEKLY) {
            $scheduled = date('Y-m-d', strtotime('next ' . $endOfWeek)) . " 23:59:59";
        } elseif ($batchType === static::BATCH_TYPE_MONTHLY) {
            $scheduled = date('Y-m-t 23:59:59');
        }

        if (!isset($scheduled)) {
            // should I log this?
            throw new \Exception("Invalid batch type: {$batchType}");
            return null;
        }
        $params = ['scheduled' => $scheduled, 'user_id' => $userId, 'broadcast_handler_id' => $handlerId];
        $batch = static::find()->where($params)->one();
        if (!$batch) {
            $batch = new static;
            $batch->attributes = $params;
            if (!$batch->save()) {
                return false;
            }
        }
        return $batch;
    }
}
