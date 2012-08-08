<h1>View ProjectToAuthAssignmentToTaskTypeToDutyType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'project_to_AuthAssignment_id',
		'task_type_to_duty_type_id',
		'staff_id',
	),
)); ?>
