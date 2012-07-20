<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_int')); ?>:</b>
	<?php echo CHtml::encode($data->type_int); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_float')); ?>:</b>
	<?php echo CHtml::encode($data->type_float); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_time')); ?>:</b>
	<?php echo CHtml::encode($data->type_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_date')); ?>:</b>
	<?php echo CHtml::encode($data->type_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_text')); ?>:</b>
	<?php echo CHtml::encode($data->type_text); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>