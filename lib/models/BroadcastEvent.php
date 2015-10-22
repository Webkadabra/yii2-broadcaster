<?php

namespace canis\broadcaster\models;

use Yii;
use canis\broadcaster\eventTypes\EventType;

/**
 * This is the model class for table "broadcast_event".
 *
 * @property string $id
 * @property string $broadcast_event_type_id
 * @property string $priority
 * @property string $object_id
 * @property resource $payload
 * @property integer $handled
 * @property string $created
 *
 * @property Registry $object
 * @property BroadcastEventType $broadcastEventType
 * @property BroadcastEventDeferred[] $broadcastEventDeferreds
 */
class BroadcastEvent extends \canis\db\ActiveRecord
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
                $this->payload = serialize($this->_dataObject);
            } catch (\Exception $e) {
                \d($this->_dataObject);
                exit;
            }
        }
    }

    /**
     * Set action object.
     *
     * @param [[@doctodo param_type:ao]] $ao [[@doctodo param_description:ao]]
     */
    public function setPayloadObject($do)
    {
        $do->model = $this;
        $this->_dataObject = $do;
    }

    /**
     * Get action object.
     *
     * @return [[@doctodo return_type:getActionObject]] [[@doctodo return_description:getActionObject]]
     */
    public function getPayloadObject()
    {
        if (!isset($this->_dataObject) && !empty($this->payload)) {
            $this->_dataObject = unserialize($this->payload);
            $this->_dataObject->model = $this;
        }

        return $this->_dataObject;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['broadcast_event_type_id'], 'required'],
            [['priority', 'payload'], 'string'],
            [['handled'], 'integer'],
            [['created'], 'safe'],
            [['broadcast_event_type_id', 'object_id'], 'string', 'max' => 36],
            [['broadcast_event_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastEventType::className(), 'targetAttribute' => ['broadcast_event_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'broadcast_event_type_id' => 'Broadcast Event Type ID',
            'priority' => 'Priority',
            'object_id' => 'Object ID',
            'payload' => 'Payload',
            'handled' => 'Handled',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Registry::className(), ['id' => 'object_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventType()
    {
        return $this->hasOne(BroadcastEventType::className(), ['id' => 'broadcast_event_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventDeferreds()
    {
        return $this->hasMany(BroadcastEventDeferred::className(), ['broadcast_event_id' => 'id']);
    }

    public function distribute($caller = null)
    {
        if (!empty($this->handled)) {
            return false;
        }
        $tableName = BroadcastSubscription::tableName();
        $query = BroadcastSubscription::find();
        $broadcaster = Yii::$app->getModule('broadcaster');
        // find subscriptions
        $params = [];
        $params[':broadcastEventTypeId'] = $this->broadcast_event_type_id;
        $where = ['and'];
        $priorities = [$this->priority];
        switch ($this->priority) {
            case EventType::PRIORITY_CRITICAL:
                $priorities[] = EventType::PRIORITY_HIGH;
            case EventType::PRIORITY_HIGH:
                $priorities[] = EventType::PRIORITY_MEDIUM;
            case EventType::PRIORITY_MEDIUM:
                $priorities[] = EventType::PRIORITY_LOW;
            break;
        }
        $where[] = '{{'.$tableName.'}}.[[minimum_priority]] IN (\''.implode("','", $priorities).'\')';;
        $eventQuery = ['or', '{{'.$tableName.'}}.[[object_id]] IS NULL'];
        if (!empty($this->object_id)) {
            $params[':objectId'] = $this->object_id;
            $eventQuery[] = '{{'.$tableName.'}}.[[object_id]]=:objectId';
        }
        $where[] = $eventQuery;
        $where[] = ['or', '{{'.$tableName.'}}.[[all_events]]=1', '{{m}}.[[broadcast_event_type_id]]=:broadcastEventTypeId'];
        $query->params = $params;
        $query->where($where)->join('LEFT JOIN', BroadcastSubscriptionEventType::tableName() .' m', '{{m}}.[[broadcast_subscription_id]]={{'.$tableName.'}}.[[id]]');

        $eventTypeModel = BroadcastEventType::get($this->broadcast_event_type_id);
        $eventType = $broadcaster->getEventType($eventTypeModel->system_id);
        if (!$eventType) {
            return false;
        }
        $failed = false;
        foreach ($query->all() as $subscription) {
            // created batch, if necessary
            $batch = null;
            if ($eventType->batchable && !empty($subscription->batch_type)) {
                $batch = BroadcastEventBatch::getBatch($subscription->user_id, $subscription->primaryKey, $subscription->batch_type);
                if ($batch === false) {
                    $failed = true;
                    continue;
                } elseif ($batch !== null) {
                    $batch = $batch->primaryKey;
                }
            }


            // create deferred items
            $attributes = [
                'broadcast_event_batch_id' => $batch,
                'broadcast_subscription_id' => $subscription->primaryKey,
                'broadcast_event_id' => $this->primaryKey
            ];

            if ($batch === null) {
                $attributes['scheduled'] = $eventType->getSchedule($this);
            }
            $deferredHandler = new BroadcastEventDeferred;
            $deferredHandler->attributes = $attributes;
            if (!$deferredHandler->save()) {
                $failed = true;
                continue;
            }
            $this->handled = true;
            $saveResult = $this->save();
            if (!$saveResult) {
                $failed = true;
            }
            if (empty($batch) && $this->priority === EventType::PRIORITY_CRITICAL) {
                if ($caller !== null && $caller instanceof \canis\base\AskInterface) {
                    if (!$caller->ask(['handle', $deferredHandler])) {
                        continue;
                    }
                }
                if (!$deferredHandler->handle()) {
                    $failed = true;
                }
            }
        }
        return !$failed;
    }
}
