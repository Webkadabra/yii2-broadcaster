<?php
namespace canis\broadcaster\eventTypes;
use canis\broadcaster\models;

class DynamicEventType extends EventType
{
	protected $_name;
	
	public function getName()
	{
		return $this->_name;
	}

	public function setName($name)
	{
		$this->_name = $name;
	}

}