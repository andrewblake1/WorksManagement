<h1>View Project #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'project_type_id',
		'travel_time_1_way',
		'critical_completion',
		'planned',
		'staff_id',
	),
)); ?>
