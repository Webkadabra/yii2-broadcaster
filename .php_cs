<?php
$path = __DIR__;
$docBlockSettings = [];
$docBlockSettings['package'] = 'infinite-notification';

return include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'yii2-infinite-core' . DIRECTORY_SEPARATOR . '.php_cs');
?>