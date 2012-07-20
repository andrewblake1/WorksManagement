<h1>View TaskToGenericTaskType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'generic_task_type_id',
		'generic_id',
		'staff_id',
	),
)); ?>
