<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('supplier_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->supplier)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('purchase_order_no')); ?>:
	<?php echo GxHtml::encode($data->purchase_order_no); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>