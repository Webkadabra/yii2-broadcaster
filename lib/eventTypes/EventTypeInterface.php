<?php
namespace canis\broadcaster\eventTypes;

interface EventTypeInterface {
	public function getSystemId();
	public function setSystemId($systemId);
	public function getName();
}