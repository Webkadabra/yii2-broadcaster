<?php
$base = get_defined_vars();
unset($base['this']);
unset($base['_file_']);
unset($base['_params_']);
echo 'Notifications'.PHP_EOL.PHP_EOL;
foreach ($items as $item) {
	$params = $base;
	$params['item'] = $item;
	echo $this->render('@canis/broadcaster/emails/default/one_plain.php', $params) . PHP_EOL;
}
?>