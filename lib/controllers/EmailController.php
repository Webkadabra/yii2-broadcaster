<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster\controllers;

class EmailController extends BaseSubscription
{
    protected function getHandler()
    {
        if (!($handler = $this->module->getHandler('email'))){
        	throw new \Exception("Unable to find email handler");
        }
        return $handler;
    }
}
