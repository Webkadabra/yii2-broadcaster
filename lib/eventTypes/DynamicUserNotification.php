<?php
namespace canis\broadcaster\eventTypes;
use canis\helpers\StringHelper;
use canis\broadcaster\models\BroadcastEvent;

class DynamicUserNotification 
	extends UserNotification
{
	protected $_subject;

	public function getSubject(BroadcastEvent $event)
	{
		if (!$this->_subject) {
			return false;
		}
		return StringHelper::simpleTwig($this->_subject, $this->getMeta($event));
	}

	public function setSubject($subject)
	{
		$this->_subject = $subject;
	}
}