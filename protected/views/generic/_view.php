<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('type_int')); ?>:
	<?php echo GxHtml::encode($data->type_int); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('type_float')); ?>:
	<?php echo GxHtml::encode($data->type_float); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('type_time')); ?>:
	<?php echo GxHtml::encode($data->type_time); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('type_date')); ?>:
	<?php echo GxHtml::encode($data->type_date); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('type_text')); ?>:
	<?php echo GxHtml::encode($data->type_text); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>