<?php
use yii\helpers\Html;
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