<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "broadcast_subscription_event_type".
 *
 * @property string $id
 * @property string $broadcast_subscription_id
 * @property string $broadcast_event_type_id
 *
 * @property BroadcastSubscription $broadcastSubscription
 * @property BroadcastEventType $broadcastEventType
 */
class BroadcastSubscriptionEventType extends \canis\db\ActiveRecordRegistry
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_subscription_event_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['broadcast_subscription_id', 'broadcast_event_type_id'], 'required'],
            [['broadcast_subscription_id', 'broadcast_event_type_id'], 'string', 'max' => 36],
            [['broadcast_subscription_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastSubscription::className(), 'targetAttribute' => ['broadcast_subscription_id' => 'id']],
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
            'broadcast_subscription_id' => 'Broadcast Subscription ID',
            'broadcast_event_type_id' => 'Broadcast Event Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastSubscription()
    {
        return $this->hasOne(BroadcastSubscription::className(), ['id' => 'broadcast_subscription_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventType()
    {
        return $this->hasOne(BroadcastEventType::className(), ['id' => 'broadcast_event_type_id']);
    }
}
