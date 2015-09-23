<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster\controllers;

use Yii;
use yii\helpers\Html;
use canis\broadcaster\models\BroadcastEventType;
use canis\broadcaster\models\BroadcastSubscription;

abstract class BaseSubscription extends \canis\web\Controller
{
	public $indexView = '@canis/broadcaster/views/base/index';
	public $createView = '@canis/broadcaster/views/base/create';
	public $deleteView = '@canis/broadcaster/views/base/delete';
	
	abstract protected function getHandler();
	abstract public function getGridViewColumns();
	abstract public function getDescriptor($singular = false);

	public function actionIndex()
    {
    	$params = [];
    	$params['handler'] = $this->handler;
    	$params['dataProvider'] = $this->handler->getSubscriptionProvider();
    	$columns = [];
    	$columns[] = [
    		'class' => 'yii\grid\ActionColumn',
    		'template' => '{update} {delete}',
    		'options' => ['style' => 'width: 50px'],
    		'buttons' => [
    			'update' => function ($url, $model, $key) {
        			return Html::a('<span class="fa fa-edit"></span>', $url, ['data-handler' => 'background']);
    			},
    			'delete' => function ($url, $model, $key) {
        			return Html::a('<span class="fa fa-trash"></span>', $url, ['data-handler' => 'background']);
    			},
    		]
    	];
    	$columns[] = ['label' => 'Item', 'attribute' => 'descriptor'];
    	$columns = array_merge($columns, $this->getGridViewColumns());
    	$columns[] = 'last_triggered:datetime';
    	$params['columns'] = $columns;
    	$params['title'] = $this->getDescriptor(false);
    	Yii::$app->response->view = $this->indexView;
    	Yii::$app->response->params = $params;
    }


    public function actionCreate()
    {
    	$params = [];
    	$params['title'] = 'Create ' . $this->getDescriptor(false);
    	$params['descriptor'] = $this->getDescriptor(true);
    	$params['eventTypes'] = [];
    	foreach ($this->module->getEventTypes() as $type) {
    		$params['eventTypes'][$type->model->primaryKey] = $type->name;
    	}
    	// \d($params);
    	$userId = Yii::$app->user->id;

    	if (isset($_GET['id'])) {
    		$action = 'update';
    		$params['model'] = $model = BroadcastSubscription::find()->where(['id' => $_GET['id']])->one();
    		if (empty($model) || $model->broadcast_handler_id !== $this->handler->model->id || $model->user_id !== $userId) {
    			throw \yii\web\NotFoundHttpException("Subscription not found!");
    		}
    	} else {
    		$action = 'create';
    		$params['model'] = $model = new BroadcastSubscription;
    		$model->broadcast_handler_id = $this->handler->model->id;
    		$model->user_id = $userId;
    		$model->all_events = 1;
    	}

    	$configClass = $this->handler->getConfigurationClass();
    	if (!$model->configObject) {
    		$model->configObject = new $configClass;
    	}

        if (!empty($_POST)) {
        	$configFormName = $model->configObject->formName();
        	$model->configObject->load($_POST);
        	$model->load($_POST);
        	if ($model->save()) {
        		Yii::$app->response->success = $this->getDescriptor(true) . ' has been '. $action .'d';
        		Yii::$app->response->refresh = true;
        		return;
        	}
        }
        Yii::$app->response->params = $params;
    	Yii::$app->response->view = $this->createView;
        Yii::$app->response->task = 'dialog';
        Yii::$app->response->taskOptions = ['title' => ucfirst($action). ' ' . $this->getDescriptor(true)];
        Yii::$app->response->labels['submit'] = ucfirst($action);
    }

    public function actionUpdate()
    {
    	return $this->actionCreate();
    }

    public function actionDelete()
    {
    	$userId = Yii::$app->user->id;
    	$this->params['title'] = 'Create ' . $this->getDescriptor(false);
    	$this->params['descriptor'] = $this->getDescriptor(true);
    	if (!empty($_GET['id'])) {
    		$model = BroadcastSubscription::find()->where(['id' => $_GET['id']])->one();
    	}
		if (empty($model) || $model->broadcast_handler_id !== $this->handler->model->id || $model->user_id !== $userId) {
			throw \yii\web\NotFoundHttpException("Subscription not found!");
		}
        $this->params['subscription'] = $model;
        if (!empty($_GET['confirm'])) {
            if ($model->delete()) {
                Yii::$app->response->refresh = true;
                Yii::$app->response->task = 'message';
                Yii::$app->response->success = $this->getDescriptor(true) . ' was deleted!';
            } else {
                Yii::$app->response->task = 'message';
                Yii::$app->response->error = 'An error occurred while deleting the ' . strtolower($this->getDescriptor(true));
            }
            return;
        }
        Yii::$app->response->taskOptions = ['title' => 'Delete ' . $this->getDescriptor(true), 'isConfirmDeletion' => true];
        Yii::$app->response->task = 'dialog';
        Yii::$app->response->view = $this->deleteView;
    }
}
