<?php

namespace canis\broadcaster\models;

use Yii;
use canis\db\behaviors\TagBehavior;

/**
 * This is the model class for table "broadcast_subscription".
 *
 * @property string $id
 * @property string $user_id
 * @property string $object_id
 * @property string $broadcast_handler_id
 * @property bool $all_events
 * @property string $name
 * @property string $batch_type
 * @property resource $config
 * @property string $created
 * @property string $last_triggered
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
    protected $_handler;

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

    public function getDescriptor()
    {
        if (!empty($this->name)) {
            return $this->name;
        }
        if (!empty($this->configObject->descriptor)) {
            return $this->configObject->descriptor;
        }
        return 'Unknown';
    }

    /**
     * [[@doctodo method_description:serializeAction]].
     */
    public function prepareConfig($event)
    {
        if (empty($this->batch_type)) {
            $this->batch_type = null;
        }
        if (isset($this->_configObject)) {
            if (!$this->configObject->validate()) {
                $this->addError('config', 'Invalid configuration');
                $event->isValid = false;
                return false;
            }
            try {
                $attributes = $this->configObject->attributes;
                unset($attributes['model']);
                $this->config = json_encode(['class' => get_class($this->configObject), 'attributes' => $attributes]);
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
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'SubscriptionEventTypes' => [
                'class' => TagBehavior::className(),
                'tagField' => 'eventTypes',
                'tagClass' => BroadcastEventType::className(),
                'viaClass' => BroadcastSubscriptionEventType::className(),
                'viaLocalField' => 'broadcast_subscription_id',
                'viaForeignField' => 'broadcast_event_type_id'
            ]
        ]);
    }

    /**
     * Get action object.
     *
     * @return [[@doctodo return_type:getActionObject]] [[@doctodo return_description:getActionObject]]
     */
    public function getConfigObject()
    {
        if (!isset($this->_configObject) && !empty($this->config)) {
            $configSettings = json_decode($this->config, true);
            if (isset($configSettings['class'])) {
                $this->_configObject = Yii::createObject($configSettings);
                $this->_configObject->model = $this;
            }
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
            [['all_events'], 'integer'],
            [['all_events'], 'checkEvents'],
            [['batch_type', 'config'], 'string'],
            [['created', 'last_triggered'], 'safe'],
            [['id', 'user_id', 'object_id', 'broadcast_handler_id'], 'string', 'max' => 36],
            [['name'], 'string', 'max' => 255],
            [['broadcast_handler_id'], 'exist', 'skipOnError' => true, 'targetClass' => BroadcastHandler::className(), 'targetAttribute' => ['broadcast_handler_id' => 'id']],
            // [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Registry::className(), 'targetAttribute' => ['object_id' => 'id']],
            // [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Registry::className(), 'targetAttribute' => ['id' => 'id']],
            //[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function checkEvents($attribute, $params)
    {
        if (empty($this->{$attribute}) && empty($this->eventTypes)) {
            $this->addError($attribute, 'You must select at least one event');
        } elseif (!empty($this->{$attribute})) {
            $this->eventTypes = [];
        }
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
            'batch_type' => 'Regularity',
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
            $handlerModel = BroadcastHandler::get($this->broadcast_handler_id);
            if ($handlerModel) {
                $this->_handler = Yii::$app->getModule('broadcaster')->getHandler($handlerModel->system_id);
            }
        }
        return $this->_handler;
    }
}
