<?php
namespace canis\broadcaster\handlers;

use Yii;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\broadcaster\models\BroadcastEvent;
use canis\broadcaster\models\BroadcastEventType;
use canis\broadcaster\models\BroadcastSubscription;

class Webhook extends BaseWebCaller
{
	public function getSystemId()
	{
		return 'webhook';
	}

    public function isAvailable()
    {
        return true;
    }
    
	public function getName()
	{
		return 'Basic Webhook';
	}

	public function getMethod(BroadcastEventDeferred $item)
	{
		return 'POST';
	}
	
    public function getConfigurationClass()
    {
        return configuration\WebhookConfiguration::className();
    }

    public function getUrl(BroadcastEventDeferred $item)
    {
    	$config = $this->getConfiguration($item);
    	return $config->url;
    }

    public function getOptions(BroadcastEventDeferred $item)
    {
    	$payload = $this->getEventPayload($item);
        $data = $payload->data;
        if (($eventType = $this->getEventType($item)) && (($event = $this->getEvent($item)))) {
            $data['meta'] = $eventType->getMeta($event);
        }
        $options = ['body' => json_encode($data), 'headers' => []];
        $options['headers']['Content-Type'] = 'application/json';
        return $options;
    }

}