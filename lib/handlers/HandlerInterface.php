<?php
namespace canis\broadcaster\handlers;

interface HandlerInterface {
	public function setSystemId($systemId);
	public function getSystemId();
	public function getName();
}