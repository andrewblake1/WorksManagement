<h1>View ProjectToAuthAssignment #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'project_id',
		'AuthAssignment_id',
		'staff_id',
	),
)); ?>
