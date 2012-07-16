<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('travel_time_1_way')); ?>:
	<?php echo GxHtml::encode($data->travel_time_1_way); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('critical_completion')); ?>:
	<?php echo GxHtml::encode($data->critical_completion); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('planned')); ?>:
	<?php echo GxHtml::encode($data->planned); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('client_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->client)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>