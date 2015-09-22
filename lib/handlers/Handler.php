<?php
namespace canis\broadcaster\handlers;
use canis\broadcaster\models;

abstract class Handler extends \yii\base\Component implements HandlerInterface
{
	protected $_model;
	protected $_systemId;

	abstract public function getName();

	public function getModel()
	{
		if (!isset($this->_model)) {
			$attributes = ['system_id' => $this->systemId];
			$this->_model = BroadcastHandler::find()->where($attributes)->one();
			if (!$this->_model) {
				$this->_model = new BroadcastHandler;
				$attributes['name'] = $this->getName();
				$this->_model->attributes = $attributes;
				if (!$this->_model->save()) {
					throw new \Exception("Broadcast handler could not be initialized!");
				}
			}
		}
		return $this->_model;
	}

	public function getSystemId()
	{
		return $this->_systemId;
	}

	public function setSystemId($systemId)
	{
		$this->_systemId = $systemId;
	}
}