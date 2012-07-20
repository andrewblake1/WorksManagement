<h1>View AuthAssignment #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'itemname',
		'userid',
		'bizrule',
		'data',
		'deleted',
		'staff_id',
	),
)); ?>
