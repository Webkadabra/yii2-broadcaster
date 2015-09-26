<?php
namespace canis\broadcaster\handlers;
use canis\broadcaster\models\BroadcastEventDeferred;

class WebClient extends Handler implements HandlerInterface
{
	public function getSystemId()
	{
		return 'web_client';
	}

	public function getName()
	{
		return 'Web Client';
	}

	public function getConfigurationClass()
    {
        return configuration\WebClientConfiguration::className();
    }

    public function isAvailable()
    {
        return false;
    }

	public function handle(BroadcastEventDeferred $item)
    {
    	return false;
    }
}