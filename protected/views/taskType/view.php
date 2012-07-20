<h1>View TaskType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'deleted',
		'staff_id',
		'template_task_id',
	),
)); ?>
