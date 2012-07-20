<h1>View Staff #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'first_name',
		'last_name',
		'phone_mobile',
		'email',
		'password',
		'deleted',
		'staff_id',
	),
)); ?>
