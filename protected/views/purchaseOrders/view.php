<h1>View PurchaseOrders #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'supplier_id',
		'purchase_order_no',
		'staff_id',
	),
)); ?>
