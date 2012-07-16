<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('lead_in_days')); ?>:
	<?php echo GxHtml::encode($data->lead_in_days); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('duty_category_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->dutyCategory)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('generic_type_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->genericType)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('deleted')); ?>:
	<?php echo GxHtml::encode($data->deleted); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>