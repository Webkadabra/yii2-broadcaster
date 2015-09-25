<?php
namespace canis\broadcaster\handlers\configuration;
abstract class EmailConfiguration extends Configuration
{
	public function validate()
	{
		return true;
	}
}