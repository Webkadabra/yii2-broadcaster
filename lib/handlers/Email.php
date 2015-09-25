<?php
namespace canis\broadcaster\handlers;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\broadcaster\models\BroadcastEventBatch;

class Email extends Handler implements HandlerInterface, BatchableHandlerInterface
{
	public function getSystemId()
	{
		return 'email';
	}

	public function getName()
	{
		return 'Email Notification';
	}

	public function getConfigurationClass()
    {
        return EmailConfiguration::className();
    }

	public function handle(BroadcastEventDeferred $item)
    {
    	return false;
    }

    public function handleBatch(BroadcastEventBatch $batch, array $deferredItems)
    {
    	return false;
    }
}