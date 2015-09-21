<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "notification_endpoint".
 *
 * @property string $id
 * @property string $user_id
 * @property string $session_id
 * @property string $notification_id
 * @property resource $endpoint
 * @property string $status
 * @property integer $background
 * @property string $error_message
 * @property string $scheduled
 * @property string $attempted
 * @property string $expires
 * @property string $created
 *
 * @property Notification $notification
 * @property User $user
 */
class NotificationEndpoint extends \canis\db\ActiveRecord
{
    protected $_endpointObject;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_endpoint';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'endpoint'], 'required'],
            [['notification_id', 'background'], 'integer'],
            [['endpoint', 'status', 'error_message'], 'string'],
            [['scheduled', 'attempted', 'expires', 'created'], 'safe'],
            [['user_id'], 'string', 'max' => 36],
            [['session_id'], 'string', 'max' => 40]
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
            'session_id' => 'Session ID',
            'notification_id' => 'Notification ID',
            'endpoint' => 'Endpoint',
            'status' => 'Status',
            'background' => 'Background',
            'error_message' => 'Error Message',
            'scheduled' => 'Scheduled',
            'attempted' => 'Attempted',
            'expires' => 'Expires',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::className(), ['id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function findMine($sessionId = null)
    {
        $query = static::find();
        $where = ['or'];
        if ($sessionId === null) {
            if (isset(Yii::$app->session)) {
                if (empty(Yii::$app->session->id)) {
                    Yii::$app->session->open();
                }
                $sessionId = Yii::$app->session->id;
            }
        }
        if (!empty($sessionId)) {
            $where[] = ['session_id' => $sessionId];
        }
        if (!Yii::$app->user->isGuest) {
            $where[] = ['user_id' => Yii::$app->user->id];
        }
        if (count($where) === 1) {
            $where[] = '1=0';
        }
        $query->where($where);
        $query->orderBy(['created' => SORT_DESC]);

        return $query;
    }

    public function getEndpointObject()
    {
        if (!isset($this->_endpointObject) && !empty($this->endpoint)) {
            $this->_endpointObject = unserialize($this->endpoint);
            $this->_endpointObject->model = $this;
        }

        return $this->_endpointObject;
    }

    public function handle()
    {
        $this->status = 'handling';
        $this->attempted = date("Y-m-d G:i:s");
        $this->save();
        if ($this->endpointObject && $this->endpointObject->handle()) {
            $this->status = 'handled';
            $this->expires = date("Y-m-d G:i:s", $this->endpointObject->getExpireTime());
        } else {
            $this->expires = date("Y-m-d G:i:s");
            $this->status = 'pending';
        }
        return $this->save();
    }
}
