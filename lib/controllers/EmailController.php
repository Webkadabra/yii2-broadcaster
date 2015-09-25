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
    public static function getLabel()
    {
        return 'Email Notifications';
    }

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
        return 'Email Notification' . (!$singular ? 's' : '');
    }


    protected function getHandlers()
    {
        if (!($handlers = $this->module->getHandlers())){
            throw new \Exception("Unable to find any handlers");
        }
        $myHandlers = [];
        foreach ($handlers as $id => $handler) {
            if ($handler instanceof \canis\broadcaster\handlers\Email) {
                $myHandlers[$id] = $handler;
            }
        }
        return $myHandlers;
    }
}
