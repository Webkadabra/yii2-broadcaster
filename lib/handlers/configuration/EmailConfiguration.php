<?php
namespace canis\broadcaster\handlers\configuration;
use Yii;

class EmailConfiguration extends Configuration
{
	public $subject;

	public function getDescriptor()
	{
		return $this->subject;
	}
	
	public function rules()
	{
		return [
            [['subject'], 'string'],
            [['subject'], 'required'],
		];
	}

	public function getAttributeConfig()
	{
		$f = [];
		$f['subject'] = [
			'type' => 'text'
		];
		return $f;
	}

	public function defaultValues()
	{
		return [
			'subject' => 'Notification from '. Yii::$app->name
		];
	}
	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => 'Email Subject',
        ];
    }
}