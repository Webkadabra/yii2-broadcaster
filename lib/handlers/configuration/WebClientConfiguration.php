<?php
namespace canis\broadcaster\handlers\configuration;
class WebClientConfiguration extends Configuration
{
	public function getDescriptor()
	{
		return null;
	}
	
	public function rules()
	{
		return [];
	}

	public function getAttributeConfig()
	{
		return [];
	}

	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}