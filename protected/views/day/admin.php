<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'scheduled',
		'preferred',
		'earliest',
		'planned',
	),
));

?>
