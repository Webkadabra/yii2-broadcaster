<?php
namespace canis\broadcaster\components;

use Yii;

class Payload extends \yii\base\Object
{
	public $model;
	public $data = [];
    public $time;
	
	public function __sleep()
    {
        $keys = array_keys((array) $this);
        $bad = ["model"];
        foreach ($keys as $k => $key) {
            if (in_array($key, $bad)) {
                unset($keys[$k]);
            }
        }

        return $keys;
    }

    public function init()
    {
        parent::init();
        $this->time = microtime(true);
    }
}