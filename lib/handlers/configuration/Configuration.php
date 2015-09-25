<?php
namespace canis\broadcaster\handlers\configuration;
abstract class Configuration extends \yii\base\Model
{
	public $model;
	
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

    abstract public function getAttributeConfig();

    public function getDescriptor()
    {
        return null;
    }
}