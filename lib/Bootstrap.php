<?php

namespace canis\broadcaster;

use yii\base\BootstrapInterface;

use canis\base\Cron;
use canis\daemon\Daemon;
use yii\base\Event;
use canis\broadcaster\Daemon as BroadcasterDaemon;

/**
 * Bootstrap [[@doctodo class_description:canis\broadcaster\Bootstrap]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Bootstrap implements BootstrapInterface
{
    public $initialHandlers = [];
    public $initialEventTypes = [];
    /**
     * [[@doctodo method_description:bootstrap]].
     *
     * @param [[@doctodo param_type:app]] $app [[@doctodo param_description:app]]
     */
    public function bootstrap($app)
    {
        $app->registerMigrationAlias('@canis/broadcaster/migrations');
        
        $app->setModule('broadcaster', ['class' => Module::className()]);
        $module = $app->getModule('broadcaster');
        $module->registerHandlers($this->initialHandlers);
        $module->registerEventTypes($this->initialEventTypes);

        Event::on(Daemon::className(), Daemon::EVENT_REGISTER_DAEMONS, [$this, 'registerDaemon']);
        
        // Event::on(Cron::className(), Cron::EVENT_WEEKLY, [$module, 'weeklyEmailDigest']);
        // Event::on(Cron::className(), Cron::EVENT_MORNING, [$module, 'dailyEmailDigest']);
    }

    public function registerDaemon($event)
    {
        $event->controller->registerDaemon('broadcaster', BroadcasterDaemon::getInstance());
    }
}
