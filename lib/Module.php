<?php
namespace canis\broadcaster;

use Yii;
use yii\base\Application;
use yii\base\Event;
use yii\helpers\Url;
use canis\broadcaster\models;

/**
 * Module [[@doctodo class_description:canis\broadcaster\Module]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Module extends \yii\base\Module
{
    const EVENT_COLLECT_EVENT_TYPES = '_collectEventTypes';
    const EVENT_COLLECT_EVENT_HANDLERS = '_collectEventHandlers';

    protected $_handlers = [];
    protected $_eventTypes = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $app = Yii::$app;
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>' => $this->id . '/<controller>/<action>',
            ], false);
        }
        $app->on(Application::EVENT_BEFORE_REQUEST, [$this, 'beforeRequest']);
        $app->on(Application::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
    }

    public function getHandler($id)
    {
        if (!isset($this->_handlers[$id])) {
            return false;
        }
        return $this->_handlers[$id];
    }

    public function getHandlers()
    {
        return $this->_handlers;
    }
    
    public function registerHandlers($handlers)
    {
        foreach ($handlers as $id => $handler) {
            $this->registerHandler($id, $handler);
        }
    }
    public function getEventType($id)
    {
        if (!isset($this->_eventTypes[$id])) {
            return false;
        }
        return $this->_eventTypes[$id];
    }
    public function getEventTypes()
    {
        return $this->_eventTypes;
    }

    public function registerEventTypes($eventTypes, $parentClass = null)
    {
        if (empty($eventTypes)) { return; }
        foreach ($eventTypes as $id => $type) {
            $this->registerEventType($id, $type, $parentClass);
        }
    }

    public function collectEventTypes($eventTypeContainers)
    {
        foreach ($eventTypeContainers as $container) {
            $reflection = new \ReflectionClass($container);
            if (!$reflection->implementsInterface(components\BroadcastableInterface::class)) {
                throw new \Exception("Event type container configuration is not valid");
            }
            $this->registerEventTypes($container::collectEventTypes(), $container);
        }
    }

    public function registerHandler($id, $handler)
    {
        if (!is_object($handler)) {
            $handler = Yii::createObject($handler);
        }
        if (!$handler || !($handler instanceof handlers\HandlerInterface)) {
            throw new \Exception("Handler configuration is not valid");
        }
        $handler->systemId = $id;
        $this->_handlers[$handler->systemId] = $handler;
    }

    static public function generateEventTypeId($id, $parentClass = null)
    {
        if ($parentClass !== null) {
            $id = substr(md5($parentClass), 0, 7) .':'. $id;
        }
        return $id;
    }
    
    public function registerEventType($id, $type, $parentClass = null)
    {
        if (!is_object($type)) {
            if (is_array($type)) {
                if (!isset($type['class'])) {
                    $type['class'] = eventTypes\DynamicEventType::className();
                }
            }
            $type = Yii::createObject($type);
        }
        if (!$type || !($type instanceof eventTypes\EventTypeInterface)) {
            throw new \Exception("Handler configuration is not valid");
        }
        $type->systemId = static::generateEventTypeId($id, $parentClass);
        $this->_eventTypes[$type->systemId] = $type;
    }

    public function beforeAction($event)
    {
        return true;
    }

    public function beforeRequest($event)
    {
        $collectEvent = new components\CollectEvent;
        $collectEvent->module = $this;
        Event::trigger(Application::className(), static::EVENT_COLLECT_EVENT_HANDLERS, $collectEvent);
        Event::trigger(Application::className(), static::EVENT_COLLECT_EVENT_TYPES, $collectEvent);
        return true;
    }


}
