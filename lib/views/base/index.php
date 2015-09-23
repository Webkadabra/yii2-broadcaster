<?php
use yii\helpers\Html;
$this->title = $title;
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading']);
echo Html::beginTag('h3', ['class' => 'panel-title']);
echo Html::beginTag('div', ['class' => 'btn-group btn-group-sm pull-right']);
echo Html::a('<span class="fa fa-plus"></span> Create', ['create'], ['class' => 'btn btn-primary', 'data-handler' => 'background']);
echo Html::endTag('div');
echo $this->title;
echo Html::endTag('h3');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'panel-body']);
echo Html::beginTag('div', ['class' => 'table-responsive']);
echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'tableOptions' => ['class' => 'table table-striped table-hover']
]);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');