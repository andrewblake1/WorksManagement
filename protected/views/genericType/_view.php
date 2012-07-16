<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('label')); ?>:
	<?php echo GxHtml::encode($data->label); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('mandatory')); ?>:
	<?php echo GxHtml::encode($data->mandatory); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('allow_new')); ?>:
	<?php echo GxHtml::encode($data->allow_new); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('validation_type_id')); ?>:
	<?php echo GxHtml::encode($data->validation_type_id); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('data_type')); ?>:
	<?php echo GxHtml::encode($data->data_type); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>