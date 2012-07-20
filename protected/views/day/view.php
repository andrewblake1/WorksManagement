<h1>View Day #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'scheduled',
		'preferred',
		'earliest',
		'planned',
		'staff_id',
	),
)); ?>
