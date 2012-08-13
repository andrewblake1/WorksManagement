<?php 

$this->widget('AdminViewWidget',array(
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
