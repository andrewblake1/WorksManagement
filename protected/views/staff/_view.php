<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('first_name')); ?>:
	<?php echo GxHtml::encode($data->first_name); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('last_name')); ?>:
	<?php echo GxHtml::encode($data->last_name); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('phone_mobile')); ?>:
	<?php echo GxHtml::encode($data->phone_mobile); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('username')); ?>:
	<?php echo GxHtml::encode($data->username); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('password')); ?>:
	<?php echo GxHtml::encode($data->password); ?>
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