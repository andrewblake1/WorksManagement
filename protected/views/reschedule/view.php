<h1>View Reschedule #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'old_task_id',
		'new_task_id',
		'staff_id',
	),
)); ?>
