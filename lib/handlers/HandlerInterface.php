<?php
namespace canis\broadcaster\handlers;

use canis\broadcaster\models\BroadcastEventDeferred;

interface HandlerInterface {
	public function setSystemId($systemId);
	public function getSystemId();
	public function getName();
	public function handle(BroadcastEventDeferred $item);
}