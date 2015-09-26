<?php
use yii\helpers\Html;
$payload = $handler->getEventPayload($item);
$event = $handler->getEvent($item);
$eventType = $handler->getEventType($item);
$meta = $eventType->getMeta($event);

echo Html::beginTag('div', ['class' => 'list-group-item']);
echo Html::beginTag('h4', ['class' => 'list-group-item-heading']);
echo $meta['descriptor'];
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'list-group-item-text']);
echo Html::tag('small', $meta['created_human']);
echo Html::endTag('div');
echo Html::endTag('div');
?>