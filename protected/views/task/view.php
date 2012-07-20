<h1>View Task #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'day',
		'purchase_orders_id',
		'crew_id',
		'project_id',
		'client_to_task_type_client_id',
		'client_to_task_type_task_type_id',
		'staff_id',
	),
)); ?>
