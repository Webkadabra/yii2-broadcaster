<?php

namespace canis\broadcaster\models;

use Yii;

/**
 * This is the model class for table "broadcast_event".
 *
 * @property string $id
 * @property string $broadcast_event_type_id
 * @property string $priority
 * @property string $object_id
 * @property resource $payload
 * @property integer $handled
 * @property string $created
 *
 * @property Registry $object
 * @property BroadcastEventType $broadcastEventType
 * @property BroadcastEventDeferred[] $broadcastEventDeferreds
 */
class BroadcastEvent extends \canis\db\ActiveRecord
{

    protected $_dataObject;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'serializeData']);
    }

    /**
     * [[@doctodo method_description:serializeAction]].
     */
    public function serializeData()
    {
        if (isset($this->_dataObject)) {
            try {
                $this->payload = serialize($this->_dataObject);
            } catch (\Exception $e) {
                \d($this->_dataObject);
                exit;
            }
        }
    }

    /**
     * Set action object.
     *
     * @param [[@doctodo param_type:ao]] $ao [[@doctodo param_description:ao]]
     */
    public function setPayloadObject($do)
    {
        $do->model = $this;
        $this->_dataObject = $do;
    }

    /**
     * Get action object.
     *
     * @return [[@doctodo return_type:getActionObject]] [[@doctodo return_description:getActionObject]]
     */
    public function getPayloadObject()
    {
        if (!isset($this->_dataObject) && !empty($this->payload)) {
            $this->_dataObject = unserialize($this->payload);
            $this->_dataObject->model = $this;
        }

        return $this->_dataObject;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'broadcast_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['broadcast_event_type_id'], 'required'],
            [['priority', 'payload'], 'string'],
            [['handled'], 'integer'],
            [['created'], 'safe'],
            [['broadcast_event_type_id', 'object_id'], 'string', 'max' => 36],
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
            'broadcast_event_type_id' => 'Broadcast Event Type ID',
            'priority' => 'Priority',
            'object_id' => 'Object ID',
            'payload' => 'Payload',
            'handled' => 'Handled',
            'created' => 'Created',
        ];
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
    public function getBroadcastEventType()
    {
        return $this->hasOne(BroadcastEventType::className(), ['id' => 'broadcast_event_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBroadcastEventDeferreds()
    {
        return $this->hasMany(BroadcastEventDeferred::className(), ['broadcast_event_id' => 'id']);
    }
}
