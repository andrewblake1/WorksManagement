<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('client_to_task_type_client_id')); ?>:</b>
	<?php echo CHtml::encode($data->client_to_task_type_client_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('client_to_task_type_task_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->client_to_task_type_task_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('generic_task_category_id')); ?>:</b>
	<?php echo CHtml::encode($data->generic_task_category_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('generic_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->generic_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deleted')); ?>:</b>
	<?php echo CHtml::encode($data->deleted); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />

	*/ ?>

</div>