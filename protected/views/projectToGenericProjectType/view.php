<h1>View ProjectToGenericProjectType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'generic_project_type_id',
		'project_id',
		'generic_id',
		'staff_id',
	),
)); ?>
