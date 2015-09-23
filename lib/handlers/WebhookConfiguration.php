<?php
namespace canis\broadcaster\handlers;
class WebhookConfiguration extends Configuration
{
	public $url;

	public function getDescriptor()
	{
		return $this->url;
	}
	
	public function rules()
	{
		return [
            [['url'], 'url'],
            [['url'], 'required'],
		];
	}

	public function getAttributeConfig()
	{
		$f = [];
		$f['url'] = [
			'type' => 'text'
		];
		return $f;
	}

	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => 'Webhook URL',
        ];
    }
}