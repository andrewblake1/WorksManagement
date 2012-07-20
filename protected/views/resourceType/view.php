<h1>View ResourceType #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'resource_category_id',
		'maximum',
		'deleted',
	),
)); ?>
