<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('resource_category_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->resourceCategory)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('maximum')); ?>:
	<?php echo GxHtml::encode($data->maximum); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('deleted')); ?>:
	<?php echo GxHtml::encode($data->deleted); ?>
	<br />

</div>