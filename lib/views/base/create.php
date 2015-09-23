<?php
use yii\helpers\Html;

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
	$(".canis-select-event-type select").selectpicker({'selectedTextFormat': 'count > 2'});
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


foreach ($model->configObject->getAttributeConfig() as $attribute => $config) {
	if (!isset($config['options'])) {
		$config['options'] = [];
	}
	if (!isset($config['type'])) {
		$config['type'] = 'text';
	}
	Html::addCssClass($config['options'], 'form-control');
	$fieldExtra = '';
	if (!empty($model->configObject->errors[$attribute])) {
		$fieldExtra = 'has-feedback has-error';
	}
	echo Html::beginTag('div', ['class' => 'form-group ' . $fieldExtra]);
	echo Html::activeLabel($model->configObject, $attribute);
	echo Html::activeInput($config['type'], $model->configObject, $attribute, $config['options']);
	echo Html::error($model->configObject, $attribute, ['class' => 'help-inline text-danger']);
	echo Html::endTag('div');
}

echo Html::endTag('div');
echo Html::endForm();