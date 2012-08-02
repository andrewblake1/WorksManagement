<h1>View GenericType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'mandatory',
		'allow_new',
		'validation_type',
		'data_type',
		'validation_text',
		'validation_error',
		'staff_id',
	),
)); ?>
