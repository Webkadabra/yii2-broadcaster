<?php
namespace canis\broadcaster\handlers;

use canis\broadcaster\models\BroadcastEventBatch;

interface BatchableHandlerInterface {

	public function handleBatch(BroadcastEventBatch $batch, array $deferredItems);
}