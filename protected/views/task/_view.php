<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('day')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->day0)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('purchase_orders_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->purchaseOrders)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('crew_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->crew)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('project_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->project)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('client_to_task_type_client_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->clientToTaskTypeClient)); ?>
	<br />
	<?php /*
	<?php echo GxHtml::encode($data->getAttributeLabel('client_to_task_type_task_type_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->clientToTaskTypeTaskType)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />
	*/ ?>

</div>