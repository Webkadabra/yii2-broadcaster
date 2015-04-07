<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\notification\widgets;

use canis\helpers\Html;
use Yii;

/**
 * NavItem [[@doctodo class_description:canis\notification\widgets\NavItem]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class NavItem extends \yii\base\Widget
{
    /**
     * @inheritdoc
     */
    public static function widget($config = [])
    {
        $config['class'] = get_called_class();
        $widget = Yii::createObject($config);
        $view = $widget->getView();
        \canis\notification\components\AssetBundle::register($view);

        $package = Yii::$app->getModule('notification')->navPackage();
        $visible = !empty($package['items']);
        $spanHtmlOptions = ['class' => 'menu-icon fa fa-inbox', 'title' => 'Notifications'];
        $htmlOptions = [];
        $linkHtmlOptions = [];
        $htmlOptions['data-notification'] = json_encode($package);
        if (!$visible) {
            Html::addCssClass($htmlOptions, 'hidden');
        }

        return ['label' => Html::tag('span', '', $spanHtmlOptions), 'url' => '#', 'options' => $htmlOptions, 'linkOptions' => $linkHtmlOptions];
    }
}
