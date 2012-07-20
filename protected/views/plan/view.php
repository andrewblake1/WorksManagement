<h1>View Plan #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'plan_name',
		'url',
		'deleted',
		'staff_id',
	),
)); ?>
