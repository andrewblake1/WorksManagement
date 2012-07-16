<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('scheduled')); ?>:
	<?php echo GxHtml::encode($data->scheduled); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('preferred')); ?>:
	<?php echo GxHtml::encode($data->preferred); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('earliest')); ?>:
	<?php echo GxHtml::encode($data->earliest); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('planned')); ?>:
	<?php echo GxHtml::encode($data->planned); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>