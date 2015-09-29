<?php
namespace canis\broadcaster\handlers;

use canis\broadcaster\models\BroadcastEventBatch;
use canis\broadcaster\models\BroadcastSubscription;

interface BatchableHandlerInterface {

	public function handleBatch(BroadcastEventBatch $batch, BroadcastSubscription $subscription, array $deferredItems);
}