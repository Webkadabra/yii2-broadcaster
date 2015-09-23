<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster\controllers;

class WebhookController extends BaseSubscription
{
    public function getGridViewColumns()
    {
        $c = [];
        $c[] = 'id';
        $c[] = 'name';
        $c[] = 'created:date';
        return $c;
    }

    public function getDescriptor($singular = false)
    {
        return 'Webhook' . (!$singular ? 's' : '');
    }

    protected function getHandler()
    {
        if (!($handler = $this->module->getHandler('webhook'))){
            throw new \Exception("Unable to find webhook handler");
        }
        return $handler;
    }
}
