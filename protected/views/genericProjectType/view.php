<h1>View GenericProjectType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'generic_type_id',
		'generic_project_category_id',
		'deleted',
		'staff_id',
	),
)); ?>
