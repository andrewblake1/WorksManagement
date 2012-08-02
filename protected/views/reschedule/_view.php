<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('old_task_id')); ?>:</b>
	<?php echo CHtml::encode($data->old_task_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('new_task_id')); ?>:</b>
	<?php echo CHtml::encode($data->new_task_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>