<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
		'mandatory',
		'allow_new',
		'validation_type',
		'data_type',
		'validation_text',
		'validation_error',
	),
));

?>
