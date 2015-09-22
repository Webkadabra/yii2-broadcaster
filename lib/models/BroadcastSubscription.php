<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "broadcast_subscription".
 *
 * @property string $id
 * @property string $user_id
 * @property string $broadcast_handler_id
 * @property string $broadcast_event_type_id
 * @property string $object_id
 * @property string $batch_type
 * @property resource $config
 * @property string $created
 *
 * @property BroadcastEventDeferred[] $broadcastEventDeferreds
 * @property BroadcastEventType $broadcastEventType
 * @property BroadcastHandler $broadcastHandler
 * @property Registry $id0
 * @property User $user
 */
class BroadcastSubscription extends \canis\db\ActiveRecordRegistry
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_subscription';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'broadcast_handler_id', 'broadcast_event_type_id'], 'required'],
            [['batch_type', 'config'], 'string'],
            [['created'], 'safe'],
            [['id', 'user_id', 'broadcast_handler_id', 'broadcast_event_type_id', 'object_id'], 'string', 'max' => 36],
            [['broadcast_event_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastEventType::className(), 'targetAttribute' => ['broadcast_event_type_id' => 'id']],
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
            'broadcast_event_type_id' => 'Broadcast Event Type ID',
            'object_id' => 'Object ID',
            'batch_type' => 'Batch Type',
            'config' => 'Config',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventDeferreds()
    {
        return $this->hasMany(BroadcastEventDeferred::className(), ['broadcast_subscription_id' => 'id']);
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
    public function getBroadcastHandler()
    {
        return $this->hasOne(BroadcastHandler::className(), ['id' => 'broadcast_handler_id']);
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
