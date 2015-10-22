<?php
use yii\helpers\Html;
use canis\broadcaster\models\BroadcastEventBatch;
use canis\broadcaster\eventTypes\EventType;

canis\web\assetBundles\BootstrapSelectAsset::register($this);

$this->title = 'Create ' . $descriptor;
echo Html::beginForm('', 'post', ['class' => 'ajax']);
echo Html::beginTag('div', ['class' => 'form']);
$fieldExtra = '';
if (!empty($model->errors['name'])) {
	$fieldExtra = 'has-feedback has-error';
}
echo Html::beginTag('div', ['class' => 'form-group ' . $fieldExtra]);
echo Html::activeLabel($model, 'name');
echo Html::activeTextInput($model, 'name', ['class' => 'form-control']);
echo Html::error($model, 'name', ['class' => 'help-inline text-danger']);
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'form-group']);
echo Html::activeRadioList($model, 'all_events', ['1' => 'All Events', '0' => 'Some Events'], ['separator' => '<br />']);
$allEventsId = Html::getInputId($model, 'all_events');
$content = <<< END
$(function() {
	$("#{$allEventsId} input").change(function() {
		if ($(this).val() == 0) {
			$(".canis-select-event-type").show();
		} else {
			$(".canis-select-event-type").hide();
		}
	});
	$(".selectpicker").selectpicker({'selectedTextFormat': 'count > 2'});
});
END;
echo Html::script($content, ['type' => 'text/javascript']);
echo Html::error($model, 'all_events', ['class' => 'help-inline text-danger']);
echo Html::endTag('div');

$fieldExtra = '';
if (!empty($model->errors['eventTypes'])) {
	$fieldExtra = 'has-feedback has-error';
}
echo Html::beginTag('div', ['style' => 'display: ' . (!empty($model->all_events) ? 'none' : 'block'), 'class' => 'canis-select-event-type form-group ' . $fieldExtra]);
echo Html::activeLabel($model, 'eventTypes');
echo Html::activeDropDownList($model, 'eventTypes', $eventTypes, ['class' => 'selectpicker form-control', 'multiple' => true]);
echo Html::error($model, 'eventTypes', ['class' => 'help-inline text-danger']);
echo Html::endTag('div');


if ($handler->getAllowMinimumPriorityFilter()) {
	$fieldExtra = '';
	if (!empty($model->errors['minimum_priority'])) {
		$fieldExtra = 'has-feedback has-error';
	}
	$priorities = [
		EventType::PRIORITY_LOW => 'Low',
		EventType::PRIORITY_MEDIUM => 'Medium',
		EventType::PRIORITY_HIGH => 'High',
		EventType::PRIORITY_CRITICAL => 'Critical'
	];
	echo Html::beginTag('div', ['class' => 'form-group ' . $fieldExtra]);
	echo Html::activeLabel($model, 'minimum_priority');
	echo Html::activeDropDownList($model, 'minimum_priority', $priorities, ['class' => 'selectpicker form-control']);
	echo Html::error($model, 'minimum_priority', ['class' => 'help-inline text-danger']);
	echo Html::endTag('div');
}

if ($handler instanceof \canis\broadcaster\handlers\BatchableHandlerInterface) {
	$fieldExtra = '';
	if (!empty($model->errors['batch_type'])) {
		$fieldExtra = 'has-feedback has-error';
	}
	$batchTypes = [
		BroadcastEventBatch::BATCH_TYPE_HOURLY => 'Hourly',
		BroadcastEventBatch::BATCH_TYPE_DAILY => 'Daily',
		BroadcastEventBatch::BATCH_TYPE_WEEKLY => 'Weekly',
		BroadcastEventBatch::BATCH_TYPE_MONTHLY => 'Monthly'
	];
	echo Html::beginTag('div', ['class' => 'form-group ' . $fieldExtra]);
	echo Html::activeLabel($model, 'batch_type');
	echo Html::activeDropDownList($model, 'batch_type', $batchTypes, ['prompt' => 'Immediately', 'class' => 'selectpicker form-control']);
	echo Html::error($model, 'batch_type', ['class' => 'help-inline text-danger']);
	echo Html::endTag('div');
}

foreach ($model->configObject->getAttributeConfig() as $attribute => $config) {
	if (!isset($config['htmlOptions'])) {
		$config['htmlOptions'] = [];
	}
	if (!isset($config['type'])) {
		$config['type'] = 'text';
	}
	if ($config['type'] === 'taggable') {
		$config['type'] = 'text';
		Html::addCssClass($config['options'], 'form-taggable');
	}
	Html::addCssClass($config['htmlOptions'], 'form-control');
	$fieldExtra = '';
	if (!empty($model->configObject->errors[$attribute])) {
		$fieldExtra = 'has-feedback has-error';
	}
	echo Html::beginTag('div', ['class' => 'form-group ' . $fieldExtra]);
	echo Html::activeLabel($model->configObject, $attribute);
	if ($config['type'] === 'select') {
		$options = isset($config['options']) ? $config['options'] : [];
		unset($config['options']);
		echo Html::activeDropDownList($model->configObject, $attribute, $options, $config['htmlOptions']);
	} else {
		echo Html::activeInput($config['type'], $model->configObject, $attribute, $config['htmlOptions']);
	}
	echo Html::error($model->configObject, $attribute, ['class' => 'help-inline text-danger']);
	echo Html::endTag('div');
}

echo Html::endTag('div');
echo Html::endForm();