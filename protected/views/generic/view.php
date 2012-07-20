<h1>View Generic #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type_int',
		'type_float',
		'type_time',
		'type_date',
		'type_text',
		'staff_id',
	),
)); ?>
