<?php 

$this->widget('AdminViewWidget',array(
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
