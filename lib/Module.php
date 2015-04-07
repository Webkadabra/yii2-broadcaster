<?php
namespace canis\notification;

use canis\notification\models\Notification;
use canis\notification\models\NotificationEndpoint;
use Yii;
use yii\base\Application;
use yii\base\Event;
use yii\helpers\Url;

/**
 * Module [[@doctodo class_description:canis\notification\Module]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Module extends \yii\base\Module
{
    protected $_distributor;
    protected $_scheduler;

    /**
     * @var [[@doctodo var_type:_active]] [[@doctodo var_description:_active]]
     */
    protected $_active;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $app = Yii::$app;
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                $this->id . '/<action:[\w\-]+>' => $this->id . '/default/<action>',
            ], false);
        }
    }

    public function getDistributor()
    {
        if (!isset($this->_distributor)) {
            $distributor = ['class' => components\Distributor::className(), 'module' => $this];
            $this->_distributor = Yii::createObject($distributor);
        }
        return $this->_distributor;
    }

    public function getScheduler()
    {
        if (!isset($this->_scheduler)) {
            $scheduler = ['class' => components\Scheduler::className(), 'module' => $this];
            $this->_scheduler = Yii::createObject($scheduler);
        }
        return $this->_scheduler;
    }

    /**
     * [[@doctodo method_description:daemonPostTick]].
     */
    public function daemonPostTick()
    {
        if (isset($this->_active)) {

        }
    }

    /**
     * [[@doctodo method_description:daemonTick]].
     *
     * @param [[@doctodo param_type:event]] $event [[@doctodo param_description:event]]
     */
    public function daemonTick($event)
    {
        $this->handleAllQueued();
    }


    /**
     * [[@doctodo method_description:pickOneQueued]].
     *
     * @return [[@doctodo return_type:pickOneQueued]] [[@doctodo return_description:pickOneQueued]]
     */
    protected function pickOneQueued()
    {
        return $this->queuedQuery->one();
    }

    protected function getQueued()
    {
        return $this->queuedQuery->all();
    }

    protected function getQueuedQuery()
    {
        $query = [
            'and', 
            ['and', 'scheduled IS NOT NULL', 'scheduled < NOW()'], 
            ['or', 'attempted IS NULL', 'attempted > DATE_SUB(DATE(), INTERVAL 1 HOUR)'], 
            ['status' => 'pending', 'background' => 1]
        ];
        return NotificationEndpoint::find()->where($query)->orderBy(['created' => SORT_ASC]);
    }

    /**
     * [[@doctodo method_description:handleOneQueued]].
     */
    protected function handleOneQueued()
    {
        $queued = $this->pickOneQueued();
        if ($queued) {
            try {
                $queued->handle();
            } catch (\Exception $e) {
                $queued = NotificationEndpoint::find()->where(['id' => $queued->id])->one();
                if ($queued) {
                    $queued->status = 'pending';
                    $message = $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage();
                    $queued->error_message .= ' Runner Exception: ' . $message;
                    $queued->save();
                }
            }
        }
    }

    protected function handleAllQueued()
    {
        $queuedItems = $this->getQueued();
        if (!empty($queued)) {
            foreach ($queuedItems as $queued) {
                try {
                    $queued->handle();
                } catch (\Exception $e) {
                    $queued = NotificationEndpoint::find()->where(['id' => $queued->id])->one();
                    if ($queued) {
                        $queued->status = 'pending';
                        $message = $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage();
                        $queued->error_message .= ' Runner Exception: ' . $message;
                        $queued->save();
                    }
                }
            }
        }
    }

    /**
     * [[@doctodo method_description:cleanActions]].
     *
     * @param [[@doctodo param_type:event]] $event [[@doctodo param_description:event]]
     */
    public function cleanActions($event)
    {
        $items = NotificationEndpoint::find()->where(['and', '`expires` < NOW()', '`status`=\'handled\''])->all();
        foreach ($items as $item) {
            $item->dismiss(false);
        }
    }

    /**
     * [[@doctodo method_description:navPackage]].
     *
     * @return [[@doctodo return_type:navPackage]] [[@doctodo return_description:navPackage]]
     */
    public function navPackage()
    {
        $package = ['_' => [], 'items' => []];
        $package['_']['refreshUrl'] = Url::to('/' . $this->id . '/nav-package');
        $package['_']['handleUrl'] = Url::to('/' . $this->id . '/handle');
        $items = NotificationEndpoint::findMine()->andWhere(['and', '`status` != "handled"', '`background` = 0', ['or', '`expires` IS NULL', '`expires` > NOW()']])->all();
        $package['items'] = [];
        foreach ($items as $item) {
            $package['items'][$item->primaryKey] = $item->package();
        }

        return $package;
    }
}
