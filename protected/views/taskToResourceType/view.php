<h1>View TaskToResourceType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'resource_type_id',
		'quantity',
		'hours',
		'start',
		'staff_id',
	),
)); ?>
