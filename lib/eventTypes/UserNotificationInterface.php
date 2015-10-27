<?php
namespace canis\broadcaster\eventTypes;
use canis\broadcaster\models\BroadcastEvent;

interface UserNotificationInterface
{
	public function handle(BroadcastEvent $broadcastEvent);
}