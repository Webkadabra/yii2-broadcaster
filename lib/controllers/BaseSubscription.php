<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster\controllers;

use Yii;
use canis\broadcaster\models\BroadcastSubscription;

abstract class BaseSubscription extends \canis\web\Controller
{
	public $indexView = '@canis/broadcaster/views/base/index';
	public $createView = '@canis/broadcaster/views/base/create';
	
	abstract protected function getHandler();
	abstract public function getGridViewColumns();
	abstract public function getDescriptor($singular = false);

	public function actionIndex()
    {
    	$params = [];
    	$params['handler'] = $this->handler;
    	$params['dataProvider'] = $this->handler->getSubscriptionProvider();
    	$params['columns'] = $this->getGridViewColumns();
    	$params['title'] = $this->getDescriptor(false);
    	Yii::$app->response->view = $this->indexView;
    	Yii::$app->response->params = $params;
    }

    public function actionCreate()
    {
    	$params = [];
    	$params['title'] = 'Create ' . $this->getDescriptor(false);
    	$params['descriptor'] = $this->getDescriptor(true);
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

    public function actionDelete()
    {
    }
}
