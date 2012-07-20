<h1>View TaskToAssembly #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'task_id',
		'assembly_id',
		'quantity',
		'staff_id',
	),
)); ?>
