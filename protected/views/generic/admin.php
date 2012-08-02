<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'type_int',
		'type_float',
		'type_time',
		'type_date',
		'type_text',
	),
));

?>
