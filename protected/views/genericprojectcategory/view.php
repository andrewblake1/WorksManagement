<!--<h1>View Genericprojectcategory #<?php echo $model->id; ?></h1>-->

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'root',
		'lft',
		'rgt',
		'level',
		'description',
		'deleted',
		'staff_id',
	),
)); ?>
