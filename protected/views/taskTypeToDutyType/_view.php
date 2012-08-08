<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('duty_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->duty_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('task_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->task_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AuthItem_name')); ?>:</b>
	<?php echo CHtml::encode($data->AuthItem_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deleted')); ?>:</b>
	<?php echo CHtml::encode($data->deleted); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>