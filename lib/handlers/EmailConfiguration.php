<?php
namespace canis\broadcaster\handlers;
abstract class EmailConfiguration extends Configuration
{
	public function validate()
	{
		return true;
	}
}