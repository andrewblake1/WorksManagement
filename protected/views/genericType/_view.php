<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('label')); ?>:</b>
	<?php echo CHtml::encode($data->label); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mandatory')); ?>:</b>
	<?php echo CHtml::encode($data->mandatory); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('allow_new')); ?>:</b>
	<?php echo CHtml::encode($data->allow_new); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('validation_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->validation_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('data_type')); ?>:</b>
	<?php echo CHtml::encode($data->data_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>