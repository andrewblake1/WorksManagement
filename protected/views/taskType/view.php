<h1>View TaskType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'description',
		'client_id',
		'deleted',
		'staff_id',
	),
)); ?>
