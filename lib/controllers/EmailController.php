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
	public function getGridViewColumns()
    {
        $c = [];
        $c[] = [
            'attribute' => 'subject',
            'label' => 'Subject',
            'format' => 'html',
            'value' => function ($model, $key, $index, $column) {
                return $model->configObject->subject;
            }
        ];
        return $c;
    }

    public function getDescriptor($singular = false)
    {
        return 'Webhook' . (!$singular ? 's' : '');
    }


    protected function getHandler()
    {
        if (!($handler = $this->module->getHandler('email'))){
        	throw new \Exception("Unable to find email handler");
        }
        return $handler;
    }
}
