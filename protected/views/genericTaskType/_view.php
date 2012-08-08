<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('task_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->task_type_id); ?>
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

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>