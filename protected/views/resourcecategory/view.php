<h1>View Resourcecategory #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'root',
		'lft',
		'rgt',
		'level',
		'duty_category_id',
		'description',
		'deleted',
		'staff_id',
	),
)); ?>
