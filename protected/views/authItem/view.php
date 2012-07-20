<h1>View AuthItem #<?php echo $model->name; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'type',
		'description',
		'bizrule',
		'data',
		'deleted',
		'staff_id',
	),
)); ?>
