<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\notification\components;

class AssetBundle extends \canis\web\assetBundles\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@canis/notification/assets';
    /**
     * @inheritdoc
     */
    public $css = ['css/canis.notification.css'];
    /**
     * @inheritdoc
     */
    public $js = [
        'js/canis.notification.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapThemeAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'canis\web\assetBundles\UnderscoreAsset',
        'canis\web\assetBundles\FontAwesomeAsset',
        'canis\web\assetBundles\AjaxFormAsset',
        'canis\web\assetBundles\CanisAsset',
    ];
}
