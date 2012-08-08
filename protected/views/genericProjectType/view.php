<h1>View GenericProjectType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'project_type_id',
		'generic_project_category_id',
		'generic_type_id',
		'deleted',
		'staff_id',
	),
)); ?>
