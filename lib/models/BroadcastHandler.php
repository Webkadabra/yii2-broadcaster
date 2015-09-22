<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "broadcast_handler".
 *
 * @property string $id
 * @property string $system_id
 * @property string $name
 *
 * @property BroadcastEventBatch[] $broadcastEventBatches
 * @property Registry $id0
 * @property BroadcastSubscription[] $broadcastSubscriptions
 */
class BroadcastHandler extends \canis\db\ActiveRecordRegistry
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_handler';
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
            [['system_id', 'name'], 'string', 'max' => 255],
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
    public function getBroadcastEventBatches()
    {
        return $this->hasMany(BroadcastEventBatch::className(), ['broadcast_handler_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Registry::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastSubscriptions()
    {
        return $this->hasMany(BroadcastSubscription::className(), ['broadcast_handler_id' => 'id']);
    }
}
