<h1>View ClientToTaskTypeToDutyType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'duty_type_id',
		'client_to_task_type_id',
		'AuthItem_name',
		'deleted',
		'staff_id',
	),
)); ?>
