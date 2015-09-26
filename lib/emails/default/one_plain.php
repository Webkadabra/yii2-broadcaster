<?php
$payload = $handler->getEventPayload($item);
$event = $handler->getEvent($item);
$eventType = $handler->getEventType($item);
$meta = $eventType->getMeta($event);
echo ' * '. $meta['created_human'] .': ' . $meta['descriptor'] . PHP_EOL;
?>