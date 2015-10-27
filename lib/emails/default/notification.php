<?php
use yii\helpers\Html;
$meta = $eventType->getMeta($event);

echo Html::beginTag('div', ['class' => 'list-group-item']);
echo Html::beginTag('h4', ['class' => 'list-group-item-heading']);
echo $meta['descriptor'];
echo Html::endTag('h4');
echo PHP_EOL;
echo Html::beginTag('div', ['class' => 'list-group-item-text']);
echo Html::tag('small', $meta['created_human']);
echo Html::endTag('div');
echo Html::endTag('div');
echo PHP_EOL;
?>