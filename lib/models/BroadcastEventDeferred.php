<?php

namespace canis\broadcaster\models;

use Yii;

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
            [['broadcast_event_id', 'broadcast_event_batch_id', 'handled'], 'integer'],
            [['result'], 'string'],
            [['scheduled', 'created'], 'safe'],
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
            'handled' => 'Handled',
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
}
