<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->beginContent($this->params['originalLayout']);
$broadcaster = Yii::$app->getModule('broadcaster');
$managers = $broadcaster->getControllerItems();
if (count($managers) > 1) {
	echo Html::beginTag('div', ['class' => 'col-md-3']);
	echo Html::beginTag('div', ['class' => '']);
	echo Html::beginTag('div', ['class' => 'list-group']);
	echo Html::tag('strong', 'Notifications', ['class' => 'list-group-item disabled']);
	foreach ($managers as $id => $label) {
		$url = Url::to(['/'. $broadcaster->friendlyUrl .'/' . $id]);
		$options = ['class' => 'list-group-item'];
		if (substr('/'.Yii::$app->request->pathInfo, 0, strlen($url)) === $url) {
			Html::addCssClass($options, 'active');
		}
		echo Html::a($label, $url, $options);
	}
	echo Html::endTag('div');
	echo Html::endTag('div');
	echo Html::endTag('div');
	echo Html::beginTag('div', ['class' => 'col-md-9']);
	echo $content;
	echo Html::endTag('div');
} else {
	echo $content;
}
$this->endContent();
