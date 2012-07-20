<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('day')); ?>:</b>
	<?php echo CHtml::encode($data->day); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('purchase_orders_id')); ?>:</b>
	<?php echo CHtml::encode($data->purchase_orders_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('crew_id')); ?>:</b>
	<?php echo CHtml::encode($data->crew_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_id')); ?>:</b>
	<?php echo CHtml::encode($data->project_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('client_to_task_type_client_id')); ?>:</b>
	<?php echo CHtml::encode($data->client_to_task_type_client_id); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('client_to_task_type_task_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->client_to_task_type_task_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />

	*/ ?>

</div>