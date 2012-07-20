<h1>View Duty #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id',
		'updated',
		'generic_id',
		'staff_id',
	),
)); ?>
