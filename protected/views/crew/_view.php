<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('preferred_date')); ?>:</b>
	<?php echo CHtml::encode($data->preferred_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('earliest_date')); ?>:</b>
	<?php echo CHtml::encode($data->earliest_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date_scheduled')); ?>:</b>
	<?php echo CHtml::encode($data->date_scheduled); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('in_charge_id')); ?>:</b>
	<?php echo CHtml::encode($data->in_charge_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>