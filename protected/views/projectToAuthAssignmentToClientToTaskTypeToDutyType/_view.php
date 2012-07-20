<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_to_AuthAssignment_id')); ?>:</b>
	<?php echo CHtml::encode($data->project_to_AuthAssignment_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('client_to_task_type_to_duty_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->client_to_task_type_to_duty_type_id); ?>
	<br />


</div>