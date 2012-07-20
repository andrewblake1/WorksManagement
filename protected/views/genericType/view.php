<h1>View GenericType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'label',
		'mandatory',
		'allow_new',
		'validation_type_id',
		'data_type',
		'staff_id',
	),
)); ?>
