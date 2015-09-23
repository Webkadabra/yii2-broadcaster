<?php
namespace canis\broadcaster\handlers;

class Webhook extends Handler implements HandlerInterface
{
	public function getSystemId()
	{
		return 'webhook';
	}

	public function getName()
	{
		return 'Webhook';
	}

	
    public function getConfigurationClass()
    {
        return WebhookConfiguration::className();
    }
}