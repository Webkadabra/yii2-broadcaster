<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster;

use Yii;
use canis\daemon\Daemon as DaemonBase;
use yii\helpers\Console;

/**
 * Daemon [[@doctodo class_description:canis\base\Daemon]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Daemon extends DaemonBase
{
	public $continue = true;
	public $headPlan = [
		'deferredHandler',
		'deferredHandler',
		'batchHandler',
		'distributor'
	];
	protected $_heads = [];

    public function getDescriptor()
    {
        return 'Broadcaster Daemon';
    }

    public function getSpeedLimit()
    {
        return 1;
    }
    
    public function run($controller, $args = [])
    {
        $_this = $this;
        $args = array_values($args);
        if (empty($args) || !in_array($args[0], ['deferredHandler', 'batchHandler', 'distributor'])) {
        	$headNumber = 0;
        	foreach ($this->headPlan as $head) {
        		$headNumber++;
        		$headId = $head .'-' . $headNumber;
        		$this->_heads[$headId] = $this->startHead($controller, $head, $headId, false);
        	}
        	$this->loop->addPeriodicTimer(5, function() use (&$_this) {
        		if (static::isPaused()) {
        			$anyRunning = false;
        			foreach ($_this->_heads as $head) {
        				if (isset($head) && $head->isRunning()) {
        					$anyRunning = true;
        				}
        			}
        			if (!$anyRunning) {
        				Yii::$app->end(0);
        			}
        		}
        	});
        	$this->loop->run();
        } elseif ($args[0] === 'deferredHandler') {
        	$this->deferredHeandlerHead();
        } elseif ($args[0] === 'batchHandler') {
        	$this->batchHeandlerHead();
        } elseif ($args[0] === 'distributor') {
        	$this->distributorHead();
        } else {
            die("Unknown sub command");
        }
    }

    public function startHead($controller, $headType, $headId, $restarting)
    {
    	if (!$restarting) {
    		// Console::output(Console::ansiFormat('Starting ' . $headId, [Console::FG_CYAN]));
    	} else {
    		// Console::output(Console::ansiFormat('Restarting ' . $headId, [Console::FG_CYAN]));
    	}
    	$_this = $this;
    	$process = new \React\ChildProcess\Process($this->getSubCommand($controller, [$headType, $headId]));
    	$process->on('exit', function($exitCode, $termSignal) use (&$_this, &$controller, $headType, $headId) {
            if ($exitCode !== 0) {
    			Console::stderr(Console::ansiFormat("Broadcast head exited with error code {$exitCode}", [Console::FG_RED]));
                sleep(10);
            }
            if (static::isPaused()) {
            	Yii::$app->end(0);
            }
            $_this->_heads[$headId] = $_this->startHead($controller, $headType, $headId, true);
        });
        $this->loop->addTimer(0.0001, function($timer) use ($process, &$_this) {
            $process->start($timer->getLoop());
            $process->stdout->on('data', function($output) use ($_this) {
                $stdout = fopen('php://stdout', 'w+');
                fwrite($stdout,$output);
            });
            $process->stderr->on('data', function($output) use ($_this) {
                $stderr = fopen('php://stderr', 'w+');
                fwrite($stderr,$output);
            });
        });
        return $process;
    }

    public function tickCallback($ticks)
    {
    	if (static::isPaused()) {
    		Yii::$app->end(0);
    	}
    	if ($ticks > 100) {
    		Yii::$app->end(0);
    	}
    }

    public function deferredHeandlerHead()
    {
    	Yii::$app->getModule('broadcaster')->deferredHandlerHead([$this, 'tickCallback']);
    }

    public function batchHeandlerHead()
    {
    	Yii::$app->getModule('broadcaster')->batchHandlerHead([$this, 'tickCallback']);
    }

    public function distributorHead()
    {
    	Yii::$app->getModule('broadcaster')->distributorHead([$this, 'tickCallback']);
    }
}
