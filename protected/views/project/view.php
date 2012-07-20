<h1>View Project #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'travel_time_1_way',
		'critical_completion',
		'planned',
		'client_id',
		'staff_id',
	),
)); ?>
