<h1>View PurchaseOrder #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'supplier_id',
		'number',
		'staff_id',
	),
)); ?>
