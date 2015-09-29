<?php
namespace canis\broadcaster\handlers;

use Yii;
use canis\broadcaster\models\BroadcastEventDeferred;
use canis\broadcaster\models\BroadcastEventBatch;
use canis\broadcaster\models\BroadcastSubscription;

class Email extends Handler implements HandlerInterface, BatchableHandlerInterface
{
	public function getSystemId()
	{
		return 'email';
	}

	public function getName()
	{
		return 'Email Notification';
	}

	public function getConfigurationClass()
    {
        return configuration\EmailConfiguration::className();
    }

    public function isAvailable()
    {
    	return isset(Yii::$app->mailer);
    }
    
    protected function discoverTo(BroadcastSubscription $subscription)
    {
        $user = $this->getUser($subscription);
        return $user->email;
    }

    protected function discoverFrom(BroadcastSubscription $subscription)
    {
        if (isset(Yii::$app->params['mail']['from'])) {
            return Yii::$app->params['mail']['from'];
        }
        return null;
    }

    protected function discoverSubject(BroadcastSubscription $subscription)
    {
        $configuration = $this->getConfiguration($subscription);
        return $configuration->subject;
    }

    protected function prepareMail(BroadcastSubscription $subscription, $mail)
    {
        return $mail->setTo($this->discoverTo($subscription))->setSubject($this->discoverSubject($subscription))->setFrom($this->discoverFrom($subscription));
    }

	public function handle(BroadcastEventDeferred $item)
    {
        $subscription = $this->getSubscriptionModel($item);
        $configuration = $this->getConfiguration($subscription);
        if (!$configuration || !$subscription) { 
            throw new \Exception("WHAT?");
            return false; 
        }
        $contentType = (isset($configuration->type) && $configuration->type === 'plain') ? 'plain' : 'rich';
        $baseAlias = '@canis/broadcaster/emails';
        Yii::$app->mailer->htmlLayout = $baseAlias . '/layouts/rich.php';
        Yii::$app->mailer->textLayout = $baseAlias . '/layouts/text.php';
        $view = [];
        if ($contentType === 'plain') {
            $view['text'] = $baseAlias .'/default/one_plain.php';
        } else {
            $view['html'] = $baseAlias .'/default/one_rich.php';
        }
        $params = [];
        $params['handler'] = $this;
        $params['item'] = $item;
        $params['subscription'] = $subscription;
        $params['configuration'] = $configuration;
        $params['subject'] = $this->discoverSubject($subscription);
        $mail = $this->prepareMail($subscription, Yii::$app->mailer->compose($view, $params));
        if (!($from = $mail->getFrom()) || empty($from)) {
            throw new \Exception("WHAT NO EMAIL?");
            return false;
        }
        return $mail->send();
    }

    public function handleBatch(BroadcastEventBatch $batch, BroadcastSubscription $subscription, array $deferredItems)
    {
        $configuration = $this->getConfiguration($subscription);
        if (!$configuration || !$subscription) { return false; }
        $contentType = (isset($configuration->type) && $configuration->type === 'plain') ? 'plain' : 'rich';
        $baseAlias = '@canis/broadcaster/emails';
        Yii::$app->mailer->htmlLayout = $baseAlias . '/layouts/rich.php';
        Yii::$app->mailer->textLayout = $baseAlias . '/layouts/text.php';
        $view = [];
        if ($contentType === 'plain') {
            $view['text'] = $baseAlias .'/default/batch_plain.php';
        } else {
            $view['html'] = $baseAlias .'/default/batch_rich.php';
        }
        $params = [];
        $params['handler'] = $this;
        $params['items'] = $deferredItems;
        $params['subscription'] = $subscription;
        $params['configuration'] = $configuration;
        $params['subject'] = $this->discoverSubject($subscription);

        $mail = $this->prepareMail($subscription, Yii::$app->mailer->compose($view, $params));
        if (!($from = $mail->getFrom()) || empty($from)) {
            return false;
        }
        $result = $mail->send();
        if (!$result) {
            
        }
        return $result;
    }
}