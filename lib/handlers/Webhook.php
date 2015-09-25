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

	public function getName()
	{
		return 'Webhook';
	}

	public function getMethod(BroadcastEventDeferred $item)
	{
		return 'POST';
	}
	
    public function getConfigurationClass()
    {
        return WebhookConfiguration::className();
    }

    public function getUrl(BroadcastEventDeferred $item)
    {
    	$config = $this->getConfiguration($item);
    	return $config->url;
    }

    public function getOptions(BroadcastEventDeferred $item)
    {
    	$payload = $this->getEventPayload($item);
    	return ['body' => json_encode($payload->data)];
    }

}