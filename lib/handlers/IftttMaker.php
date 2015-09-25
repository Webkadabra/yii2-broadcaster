<?php
namespace canis\broadcaster\handlers;

use Yii;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\broadcaster\models\BroadcastEvent;
use canis\broadcaster\models\BroadcastEventType;
use canis\broadcaster\models\BroadcastSubscription;
use canis\helpers\StringHelper;

class IftttMaker extends Webhook
{
	public function getSystemId()
	{
		return 'ifttt_maker';
	}

	public function getName()
	{
		return 'IFTTT Maker';
	}

    public function getConfigurationClass()
    {
        return configuration\IftttMakerConfiguration::className();
    }


    public function getOptions(BroadcastEventDeferred $item)
    {
        $config = $this->getConfiguration($item);
    	$payload = $this->getEventPayload($item);
        $data = $payload->data;
        if (($eventType = $this->getEventType($item)) && (($event = $this->getEvent($item)))) {
            $data['meta'] = $eventType->getMeta($event);
        }
        $payload = [];
        $payload['value1'] = StringHelper::simpleTwig($config->value1, $data);
        $payload['value2'] = StringHelper::simpleTwig($config->value2, $data);
        $payload['value3'] = StringHelper::simpleTwig($config->value3, $data);
        $options = ['body' => json_encode($payload), 'headers' => []];
        $options['headers']['Content-Type'] = 'application/json';
    	return $options;
    }

}