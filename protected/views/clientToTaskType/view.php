<h1>View ClientToTaskType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'client_id',
		'task_type_id',
		'deleted',
		'staff_id',
	),
)); ?>
