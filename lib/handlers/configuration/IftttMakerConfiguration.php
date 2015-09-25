<?php
namespace canis\broadcaster\handlers\configuration;
class IftttMakerConfiguration extends WebhookConfiguration
{
	public $value1;
	public $value2;
	public $value3;
	
	public function rules()
	{
		return array_merge(parent::rules(), [
            [['value1', 'value2', 'value3'], 'string']
		]);
	}

	public function getAttributeConfig()
	{
		$f = parent::getAttributeConfig();
		$f['value1'] = [
			'type' => 'taggable'
		];
		$f['value2'] = [
			'type' => 'taggable'
		];
		$f['value3'] = [
			'type' => 'taggable'
		];
		return $f;
	}

	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'value1' => 'Payload Value 1',
            'value2' => 'Payload Value 2',
            'value3' => 'Payload Value 3',
        ]);
    }
}