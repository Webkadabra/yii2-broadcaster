<?php
namespace canis\broadcaster;

use Yii;
use yii\base\Application;
use yii\base\Event;
use yii\helpers\Url;
use canis\broadcaster\models\BroadcastEvent;
use canis\broadcaster\models\BroadcastEventBatch;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\caching\Cacher;

/**
 * Module [[@doctodo class_description:canis\broadcaster\Module]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Module 
    extends \yii\base\Module
    implements \canis\base\AskInterface
{
    const EVENT_COLLECT_EVENT_TYPES = '_collectEventTypes';
    const EVENT_COLLECT_EVENT_HANDLERS = '_collectEventHandlers';

    protected $_handlers = [];
    protected $_eventTypes = [];
    protected $_defaultHandler;

    public $friendlyUrl = 'notification';
    public $allowMinimumPriorityFilter = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $app = Yii::$app;
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                $this->friendlyUrl . '' => $this->id . '/default/index',
                $this->friendlyUrl . '/<controller:[\w\-]+>' => $this->id . '/<controller>/index',
                $this->friendlyUrl . '/<controller:[\w\-]+>/<action:[\w\-]+>' => $this->id . '/<controller>/<action>',
            ], false);
            
            $controllers = [
                'email' => controllers\EmailController::className(),
                'webhook' => controllers\WebhookController::className()
            ];
            if (isset(Yii::$app->params['broadcastControllers'])) {
                $controllers = array_merge($controllers, Yii::$app->params['broadcastControllers']);
            }
            foreach ($controllers as $id => $controller) {
                if (!$controller) { continue; }
                $this->controllerMap[$id] = $controller;
            }
        }
        $app->on(Application::EVENT_BEFORE_REQUEST, [$this, 'beforeRequest']);
        $app->on(Application::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
    }


    public function getControllerItems()
    {
        $items = [];
        foreach ($this->controllerMap as $id => $controller) {
            $reflection = new \ReflectionClass($controller);
            if ($reflection->implementsInterface(controllers\SubscriptionManagerInterface::class)) {
                $items[$id] = $controller::getLabel();
            }
        }
        return $items;
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
            if (!$reflection->implementsInterface(BroadcastableInterface::class)) {
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

    public function deferredHandlerHead($tickCallback, $limitPerTick = 10)
    {
        $ticks = 0;
        $tableName = BroadcastEventDeferred::tableName();
        $queryWhere = ['and', '{{'.$tableName.'}}.[[broadcast_event_batch_id]] IS NULL', '{{'.$tableName.'}}.[[started]] IS NULL', ['or', '{{'.$tableName.'}}.[[scheduled]] IS NULL', '{{'.$tableName.'}}.[[scheduled]] < UTC_TIMESTAMP()']];
        $query = BroadcastEventDeferred::find()->orderBy(['{{e}}.[[priority]]' => SORT_DESC])->where($queryWhere)->limit($limitPerTick);
        $query->join('INNER JOIN', BroadcastEvent::tableName() . ' e', '{{e}}.[[id]]={{'.$tableName.'}}.[[broadcast_event_id]]');
        while(true) {
            $ticks++;
            $deferredItems = $query->all();
            $skipCount = 0;
            $itemCount = count($deferredItems);
            $sleepAfter = $itemCount === 0;
            foreach ($deferredItems as $item) {
                if ($this->checkFail('BroadcastEventDeferred.'.$item->id)) {
                    continue;
                }
                if(!$item->handle()) {
                    $sleepAfter = true;
                }
            }
            if ($skipCount !== 0 && $skipCount === $limitPerTick ) {
                Yii::$app->end(1);
            }
            call_user_func($tickCallback, $ticks);
            if ($sleepAfter) {
                sleep(5);
            }
        }
    }

    public function batchHandlerHead($tickCallback, $limitPerTick = 10)
    {
        $ticks = 0;
        $tableName = BroadcastEventBatch::tableName();
        $queryWhere = ['and', '{{'.$tableName.'}}.[[started]] IS NULL', ['or', '{{'.$tableName.'}}.[[scheduled]] IS NULL', '{{'.$tableName.'}}.[[scheduled]] < UTC_TIMESTAMP()']];
        $query = BroadcastEventBatch::find()->where($queryWhere)->limit($limitPerTick);
        while(true) {
            $ticks++;
            $batches = $query->all();
            $skipCount = 0;
            $itemCount = count($batches);
            $sleepAfter = $itemCount === 0;
            foreach ($batches as $batch) {
                if ($this->checkFail('BroadcastEventBatch.'.$batch->id)) {
                    continue;
                }
                if(!$batch->handle()) {
                    $sleepAfter = true;
                }
            }
            if ($skipCount !== 0 && $skipCount === $limitPerTick ) {
                Yii::$app->end(1);
            }
            call_user_func($tickCallback, $ticks);
            if ($sleepAfter) {
                sleep(5);
            }
            Yii::$app->end(0);
        }
    }

    public function ask($what)
    {
        if (!is_array($what)) {
            return true;
        }
        if (isset($what[0]) && isset($what[1]) && $what[0] === 'handle') {
            if ($what[1] instanceof BroadcastEvent) {
                return !$this->checkFail('BroadcastEvent.'.$what[1]->id);
            }
            if ($what[1] instanceof BroadcastEventBatch) {
                return !$this->checkFail('BroadcastEventBatch.'.$what[1]->id);
            }
            if ($what[1] instanceof BroadcastEventDeferred) {
                return !$this->checkFail('BroadcastEventDeferred.'.$what[1]->id);
            }
        }
        return true;
    }

    public function distributorHead($tickCallback, $limitPerTick = 40)
    {
        $ticks = 0;
        while(true) {
            $ticks++;
            $eventsToDistribute = BroadcastEvent::find()->where(['handled' => 0])->orderBy(['priority' => SORT_DESC])->limit($limitPerTick)->all();
            $skipCount = 0;
            $itemCount = count($eventsToDistribute);
            $sleepAfter = $itemCount === 0;
            foreach ($eventsToDistribute as $event) {
                if ($this->checkFail('BroadcastEvent.'.$event->id)) {
                    $skipCount++;
                    continue;
                }
                if(!$event->handle($this)) {
                    $sleepAfter = true;
                }
            }
            if ($skipCount === $limitPerTick ) {
                Yii::$app->end(1);
            }
            call_user_func($tickCallback, $ticks);
            if ($sleepAfter) {
                sleep(5);
            }
        }
    }

    public function setDefaultHandler($defaultHandler)
    {
        $this->_defaultHandler = $defaultHandler;
    }

    public function getDefaultHandler()
    {
        if (isset($this->_defaultHandler)) {
            return $this->_defaultHandler;
        }
        foreach ($this->controllerMap as $id => $controller) {
            return $id;
        }
        return null;
    }

    private function checkFail($id)
    {
        $key = __CLASS__ . __FUNCTION__ . $id;
        if (Cacher::get($key) === true) {
            return true;
        }
        Cacher::set($key, true, 60);
        return false;
    }
}
