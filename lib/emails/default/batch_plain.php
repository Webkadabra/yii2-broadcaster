<?php
$base = get_defined_vars();
foreach ($items as $item) {
	$params = $base;
	$params['item'] = $item;
	echo $this->renderFile(__DIR__ . DIRECTORY_SEPARATOR . 'one_plain.php', $params);
}
?>