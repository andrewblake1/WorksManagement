<h1>View MaterialToTask #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'material_id',
		'task_id',
		'quantity',
		'staff_id',
	),
)); ?>
