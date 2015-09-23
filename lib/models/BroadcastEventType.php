<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "broadcast_event_type".
 *
 * @property string $id
 * @property string $system_id
 * @property string $name
 *
 * @property BroadcastEvent[] $broadcastEvents
 * @property Registry $id0
 */
class BroadcastEventType extends \canis\db\ActiveRecordRegistry
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_event_type';
    }
    
    /**
     * @inheritdoc
     */
    public static function isAccessControlled()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['system_id', 'name'], 'required'],
            [['id'], 'string', 'max' => 36],
            [['system_id', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_id' => 'System ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEvents()
    {
        return $this->hasMany(BroadcastEvent::className(), ['broadcast_event_type_id' => 'id']);
    }

    /** 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getBroadcastSubscriptions() 
   { 
       return $this->hasMany(BroadcastSubscription::className(), ['broadcast_event_type_id' => 'id']); 
   } 
   public function getBroadcastSubscriptionEventTypes()
   {
       return $this->hasMany(BroadcastSubscriptionEventType::className(), ['broadcast_event_type_id' => 'id']);
   }
}
