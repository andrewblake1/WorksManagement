<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('scheduled')); ?>:</b>
	<?php echo CHtml::encode($data->scheduled); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('preferred')); ?>:</b>
	<?php echo CHtml::encode($data->preferred); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('earliest')); ?>:</b>
	<?php echo CHtml::encode($data->earliest); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('planned')); ?>:</b>
	<?php echo CHtml::encode($data->planned); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>