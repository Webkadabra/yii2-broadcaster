<?php
namespace canis\broadcaster\eventTypes;

use Yii;
use canis\broadcaster\models\BroadcastEvent;

abstract class UserNotification 
	extends DynamicEventType
	implements UserNotificationInterface
{
	abstract public function getSubject(BroadcastEvent $broadcastEvent);

	public function getRequiredPayloadKeys()
	{
		return ['_user'];
	}

	public function handle(BroadcastEvent $broadcastEvent)
	{
		$contentType = 'rich';
        $baseAlias = '@canis/broadcaster/emails';
        Yii::$app->mailer->htmlLayout = $baseAlias . '/layouts/rich.php';
        Yii::$app->mailer->textLayout = $baseAlias . '/layouts/text.php';
        $view = [];
        $view['html'] = $baseAlias .'/default/notification.php';
        $params = [];
        $params['eventType'] = $this;
        $params['event'] = $broadcastEvent;
        $params['subject'] = $this->getSubject($broadcastEvent);
        $mail = $this->prepareMail($broadcastEvent, Yii::$app->mailer->compose($view, $params));
        if (!$mail || !($from = $mail->getFrom()) || empty($from)) {
            return false;
        }
        return $mail->send();
	}
    
    public function getDescriptorMeta(BroadcastEvent $broadcastEvent)
    {
        $meta = parent::getDescriptorMeta($broadcastEvent);
        $user = $this->getUser($broadcastEvent);
        if ($user) {
            $meta['_user'] = $user->attributes;
            $meta['_user']['descriptor'] = $user->descriptor;
        }
        return $meta;
    }

    public function getMeta(BroadcastEvent $broadcastEvent)
    {
        $meta = parent::getMeta($broadcastEvent);
        if (!$meta) {
            return false;
        }
        $user = $this->getUser($broadcastEvent);
        if ($user) {
            $meta['_user'] = $user->attributes;
            $meta['_user']['descriptor'] = $user->descriptor;
        }
        return $meta;
    }

	protected function discoverTo(BroadcastEvent $broadcastEvent)
    {
        $user = $this->getUser($broadcastEvent);
        if (!$user) {
        	return false;
        }
        return $user->email;
    }

    protected function prepareMail(BroadcastEvent $broadcastEvent, $mail)
    {
    	$to = $this->discoverTo($broadcastEvent);
    	$from = $this->discoverFrom($broadcastEvent);
    	$subject = $this->getSubject($broadcastEvent);
    	if (!$to || !$from || !$subject) {
    		return false;
    	}
        return $mail->setTo($to)->setSubject($subject)->setFrom($from);
    }

    protected function discoverFrom(BroadcastEvent $broadcastEvent)
    {
        if (isset(Yii::$app->params['mail']['from'])) {
            return Yii::$app->params['mail']['from'];
        }
        return null;
    }

	public function getUser(BroadcastEvent $broadcastEvent)
    {
        $userClass = Yii::$app->classes['User'];
        if (!($user = $userClass::get($broadcastEvent->getPayloadObject()->data['_user']))) {
        	return false;
        }
        return $user;
    }
}