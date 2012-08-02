<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'name',
		'type',
		'description',
		'bizrule',
		'data',
	),
));

?>
