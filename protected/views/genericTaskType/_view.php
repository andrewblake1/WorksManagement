<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('client_to_task_type_client_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->clientToTaskTypeClient)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('client_to_task_type_task_type_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->clientToTaskTypeTaskType)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('generic_task_category_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->genericTaskCategory)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('generic_type_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->genericType)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('deleted')); ?>:
	<?php echo GxHtml::encode($data->deleted); ?>
	<br />
	<?php /*
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />
	*/ ?>

</div>