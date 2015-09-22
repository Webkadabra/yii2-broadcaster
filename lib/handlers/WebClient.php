<?php
namespace canis\broadcaster\handlers;

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
}