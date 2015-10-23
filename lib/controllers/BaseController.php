<?php
/**
 * @link http://canis.io
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use canis\web\unifiedMenu\Menu;

class BaseController 
    extends \canis\web\Controller
    implements \canis\web\unifiedMenu\MenuProviderInterface
{

    public static function provideMenuItems(Menu $menu)
    {
        $menu = [];
        $broadcaster = Yii::$app->getModule('broadcaster');
        $managers = $broadcaster->getControllerItems();
        foreach ($managers as $id => $label) {
            $item = [];
            $item['url'] = ['/'. $broadcaster->friendlyUrl .'/' . $id];
            $item['label'] = $label;
            $menu['broadcaster-'.$id] = $item;
        }
        return $menu;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'UnifiedMenu' => [
                'class' => \canis\web\unifiedMenu\ControllerBehavior::className(),
                'providingController' => BaseController::className()
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
?>