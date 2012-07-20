<h1>View DutyType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'lead_in_days',
		'duty_category_id',
		'generic_type_id',
		'deleted',
		'staff_id',
	),
)); ?>
