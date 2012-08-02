<h1>View Crew #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'preferred_date',
		'earliest_date',
		'date_scheduled',
		'in_charge_id',
		'staff_id',
	),
)); ?>
