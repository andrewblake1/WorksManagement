<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('preferred_date')); ?>:
	<?php echo GxHtml::encode($data->preferred_date); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('earliest_date')); ?>:
	<?php echo GxHtml::encode($data->earliest_date); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('date_scheduled')); ?>:
	<?php echo GxHtml::encode($data->date_scheduled); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('in_charge')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->inCharge)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>