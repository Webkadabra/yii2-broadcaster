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

class DefaultController extends BaseController
{
    public function actionIndex()
    {
        $broadcaster = Yii::$app->getModule('broadcaster');
        $this->redirect(['/' . $broadcaster->friendlyUrl .'/'.$broadcaster->defaultHandler]);
    }
}
