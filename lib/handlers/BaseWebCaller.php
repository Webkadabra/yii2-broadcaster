<?php
namespace canis\broadcaster\handlers;

use Yii;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\broadcaster\models\BroadcastEvent;
use canis\broadcaster\models\BroadcastEventType;
use canis\broadcaster\models\BroadcastSubscription;

abstract class BaseWebCaller extends Handler implements HandlerInterface
{
    abstract public function getUrl(BroadcastEventDeferred $item);
    abstract public function getOptions(BroadcastEventDeferred $item);
    abstract public function getMethod(BroadcastEventDeferred $item);

    public function getEventPayload(BroadcastEventDeferred $item)
    {
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = BroadcastEvent::get($item->broadcast_event_id))) {
            $item->fail("Event is invalid");
            return false;
        }
        return $event->payloadObject;
    }

    public function getConfiguration(BroadcastEventDeferred $item)
    {
        $result = $item->resultObject;
        $broadcaster = Yii::$app->getModule('broadcaster');
        if (!($event = BroadcastEvent::get($item->broadcast_event_id))) {
            $item->fail("Event is invalid");
            return false;
        }
        if (!($subscriptionModel = BroadcastSubscription::get($item->broadcast_subscription_id))) {
            $item->fail("Subscription model is invalid");
            return false;
        }
        if (!($configuration = $subscriptionModel->configObject)) {
            $item->fail("Configuration object is invalid");
            return false;
        }
        return $configuration;
    }

    public function handle(BroadcastEventDeferred $item)
    {
        return $this->makeWebCall($this->getMethod($item), $this->getUrl($item), $this->getOptions($item));
    }

    private function makeWebCall($method, $url, $options = [])
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request($method, $url, $options);
        return $res->getStatusCode() === 200;
    }
}