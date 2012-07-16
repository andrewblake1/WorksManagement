<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('generic_type_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->genericType)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('generic_project_category_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->genericProjectCategory)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('deleted')); ?>:
	<?php echo GxHtml::encode($data->deleted); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />

</div>