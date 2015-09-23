<?php
use yii\helpers\Html;
$this->title = 'Delete ' . $descriptor;
echo Html::beginForm('', 'get', ['class' => 'ajax']);
echo Html::beginTag('div', ['class' => 'form']);
echo Html::hiddenInput('certificate', $subscription->id);
echo Html::hiddenInput('confirm', 1);
echo Html::tag('div', 'Are you sure you want to delete the '. strtolower($descriptor) .' <i>'. $subscription->descriptor .'</i>?', ['class' => 'alert alert-warning']);
echo Html::endTag('div');
echo Html::endForm();