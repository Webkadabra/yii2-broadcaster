<?php

namespace canis\broadcaster\models;

use Yii;

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
            [['handled'], 'integer'],
            [['scheduled', 'created'], 'safe'],
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
            'handled' => 'Handled',
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
}
