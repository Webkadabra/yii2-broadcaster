<?php
use yii\helpers\Html;
$base = get_defined_vars();
unset($base['this']);
unset($base['_file_']);
unset($base['_params_']);

echo Html::tag('strong', 'Notifications', ['class' => 'list-group-item disabled']);

foreach ($items as $item) {
	$params = $base;
	$params['item'] = $item;
	echo $this->render('@canis/broadcaster/emails/default/one_rich.php', $params);
}
?>