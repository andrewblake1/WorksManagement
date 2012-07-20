<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('task_old')); ?>:</b>
	<?php echo CHtml::encode($data->task_old); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('task_new')); ?>:</b>
	<?php echo CHtml::encode($data->task_new); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>