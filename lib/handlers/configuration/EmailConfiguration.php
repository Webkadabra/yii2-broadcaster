<?php
namespace canis\broadcaster\handlers\configuration;
use Yii;

class EmailConfiguration extends Configuration
{
	public $subject;
	public $type;

	public function getDescriptor()
	{
		return $this->subject;
	}
	
	public function rules()
	{
		return [
            [['subject', 'type'], 'string'],
            [['subject', 'type'], 'required'],
		];
	}

	public function getAttributeConfig()
	{
		$f = [];
		$f['subject'] = [
			'type' => 'text'
		];
		$f['type'] = [
			'type' => 'select',
			'options' => [
				'rich' => 'Rich Text',
				'plain' => 'Plain Text'
			]
		];
		return $f;
	}

	public function defaultValues()
	{
		return [
			'subject' => 'Notification from '. Yii::$app->name,
			'type' => 'rich'
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