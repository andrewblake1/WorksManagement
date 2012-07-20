<h1>View Reschedule #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_old',
		'task_new',
		'staff_id',
	),
)); ?>
