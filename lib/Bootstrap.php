<?php

namespace canis\notification;

use yii\base\BootstrapInterface;

use canis\base\Cron;
use canis\base\Daemon;
use yii\base\Event;

/**
 * Bootstrap [[@doctodo class_description:canis\notification\Bootstrap]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * [[@doctodo method_description:bootstrap]].
     *
     * @param [[@doctodo param_type:app]] $app [[@doctodo param_description:app]]
     */
    public function bootstrap($app)
    {
        $app->registerMigrationAlias('@canis/notification/migrations');
        
        $app->setModule('notification', ['class' => Module::className()]);
        $module = $app->getModule('notification');
        // Event::on(Daemon::className(), Daemon::EVENT_TICK, [$module, 'daemonTick']);
        // Event::on(Daemon::className(), Daemon::EVENT_POST_TICK, [$module, 'daemonPostTick']);
        
        // Event::on(Cron::className(), Cron::EVENT_WEEKLY, [$module, 'weeklyEmailDigest']);
        // Event::on(Cron::className(), Cron::EVENT_MORNING, [$module, 'dailyEmailDigest']);
    }
}
