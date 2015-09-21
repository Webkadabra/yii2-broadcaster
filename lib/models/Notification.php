<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property string $hash
 * @property resource $notification
 * @property string $created
 *
 * @property NotificationEndpoint[] $notificationEndpoints
 */
class Notification extends \canis\db\ActiveRecord
{
    protected $_notificationObject;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification'], 'required'],
            [['notification'], 'string'],
            [['created'], 'safe'],
            [['hash'], 'string', 'max' => 40]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
            'notification' => 'Notification',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationEndpoints()
    {
        return $this->hasMany(NotificationEndpoint::className(), ['notification_id' => 'id']);
    }


    public function getNotificationObject()
    {
        if (!isset($this->_notificationObject) && !empty($this->notification)) {
            $this->_notificationObject = unserialize($this->notification);
            $this->_notificationObject->model = $this;
        }

        return $this->_notificationObject;
    }
}
