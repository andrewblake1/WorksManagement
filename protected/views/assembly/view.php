<h1>View Assembly #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'url',
		'material_id',
		'quantity',
		'deleted',
		'staff_id',
	),
)); ?>
