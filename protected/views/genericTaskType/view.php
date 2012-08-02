<h1>View GenericTaskType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'client_to_task_type_id',
		'description',
		'generic_task_category_id',
		'generic_type_id',
		'deleted',
		'staff_id',
	),
)); ?>
