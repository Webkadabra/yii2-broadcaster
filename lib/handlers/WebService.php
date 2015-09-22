<?php
namespace canis\broadcaster\handlers;

class WebService extends Handler implements HandlerInterface
{
	public function getSystemId()
	{
		return 'web_service';
	}

	public function getName()
	{
		return 'Web Service';
	}
}