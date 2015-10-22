<?php
use yii\helpers\Html;

canis\web\assetBundles\BootstrapSelectAsset::register($this);
$this->title = $title;
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading']);
echo Html::beginTag('h3', ['class' => 'panel-title']);
echo Html::beginTag('div', ['class' => 'btn-group btn-group-sm pull-right']);
if (count($handlers) > 1) {
	echo Html::a('<span class="fa fa-plus"></span> Create', ['#'], ['class' => 'btn btn-primary dropdown-toggle', 'data-toggle' => 'dropdown']);
	echo Html::beginTag('ul', ['class' => 'dropdown-menu']);
	foreach ($handlers as $id => $handler) {
		echo Html::tag('li', Html::a($handler->name, ['create', 'handler' => $id], ['class' => '', 'data-handler' => 'background']));
	}
	echo Html::endTag('ul');
} else {
	foreach ($handlers as $id => $handler) {
		echo Html::a('<span class="fa fa-plus"></span> Create', ['create', 'handler' => $id], ['class' => 'btn btn-primary', 'data-handler' => 'background']);
	}
}

echo Html::endTag('div');
echo $this->title;
echo Html::endTag('h3');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'panel-body']);
echo Html::beginTag('div', ['class' => 'table-responsive']);
echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'formatter' => [
    	'class' => 'yii\i18n\Formatter',
    	'nullDisplay' => '<span class="not-set">(none)</span>'
    ],
    'tableOptions' => ['class' => 'table table-striped table-hover']
]);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');