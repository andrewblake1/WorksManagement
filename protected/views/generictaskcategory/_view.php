<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('root')); ?>:
	<?php echo GxHtml::encode($data->root); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('lft')); ?>:
	<?php echo GxHtml::encode($data->lft); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('rgt')); ?>:
	<?php echo GxHtml::encode($data->rgt); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('level')); ?>:
	<?php echo GxHtml::encode($data->level); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('name')); ?>:
	<?php echo GxHtml::encode($data->name); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('deleted')); ?>:
	<?php echo GxHtml::encode($data->deleted); ?>
	<br />
	<?php /*
	<?php echo GxHtml::encode($data->getAttributeLabel('staff_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->staff)); ?>
	<br />
	*/ ?>

</div>