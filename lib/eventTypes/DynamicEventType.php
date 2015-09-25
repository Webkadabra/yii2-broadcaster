<?php
namespace canis\broadcaster\eventTypes;
use canis\broadcaster\models;

class DynamicEventType extends EventType
{
	protected $_name;
	protected $_batchable = true;
	
	public function getName()
	{
		return $this->_name;
	}

	public function setName($name)
	{
		$this->_name = $name;
	}

	public function getBatchable()
	{
		return $this->_batchable;
	}

	public function setBatchable($batchable)
	{
		$this->_batchable = $batchable;
	}
}