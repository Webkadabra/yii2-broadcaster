<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "broadcast_subscription".
 *
 * @property string $id
 * @property string $user_id
 * @property string $object_id
 * @property string $broadcast_handler_id
 * @property string $name
 * @property string $batch_type
 * @property resource $config
 * @property string $created
 *
 * @property BroadcastEventDeferred[] $broadcastEventDeferreds
 * @property BroadcastHandler $broadcastHandler
 * @property Registry $object
 * @property Registry $id0
 * @property User $user
 * @property BroadcastSubscriptionEventType[] $broadcastSubscriptionEventTypes
 */
class BroadcastSubscription extends \canis\db\ActiveRecordRegistry
{
    protected $_configObject;

    /**
     * @todo probably want to re-enable this after we fix access control
     */
    public static function isAccessControlled()
    {
        return false;
    }

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'prepareConfig']);
    }

    /**
     * [[@doctodo method_description:serializeAction]].
     */
    public function prepareConfig($event)
    {
        if (isset($this->_configObject)) {
            if (!$this->configObject->validate()) {
                $this->addError('config', 'Invalid configuration');
                $event->isValid = false;
                return false;
            }
            try {
                $this->config = serialize($this->_configObject);
            } catch (\Exception $e) {
                \d($this->_configObject);
                exit;
            }
        } else {
            $this->addError('config', 'Need to provide configuration for this subscription');
            $event->isValid = false;
            return false;
        }
    }

    /**
     * Get action object.
     *
     * @return [[@doctodo return_type:getActionObject]] [[@doctodo return_description:getActionObject]]
     */
    public function getConfigObject()
    {
        if (!isset($this->_configObject) && !empty($this->config)) {
            $this->_configObject = unserialize($this->config);
            $this->_configObject->model = $this;
        }

        return $this->_configObject;
    }

    public function setConfigObject($configObject)
    {
        $configObject->model = $this;
        $this->_configObject = $configObject;
    }

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
            [['user_id', 'broadcast_handler_id'], 'required'],
            [['batch_type', 'config'], 'string'],
            [['created'], 'safe'],
            [['id', 'user_id', 'object_id', 'broadcast_handler_id'], 'string', 'max' => 36],
            [['name'], 'string', 'max' => 255],
            [['broadcast_handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastHandler::className(), 'targetAttribute' => ['broadcast_handler_id' => 'id']],
            // [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Registry::className(), 'targetAttribute' => ['object_id' => 'id']],
            // [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Registry::className(), 'targetAttribute' => ['id' => 'id']],
            //[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'object_id' => 'Object ID',
            'broadcast_handler_id' => 'Broadcast Handler ID',
            'name' => 'Name',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastSubscriptionEventTypes()
    {
        return $this->hasMany(BroadcastSubscriptionEventType::className(), ['broadcast_subscription_id' => 'id']);
    }

    public function getHandler()
    {
        if (!isset($this->_handler)) {

        }
        return $this->_handler;
    }
}
