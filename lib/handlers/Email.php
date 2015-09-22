<?php
namespace canis\broadcaster\handlers;

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
}