<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'id'); ?>
		<?php echo $form->textField($model, 'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'root'); ?>
		<?php echo $form->textField($model, 'root'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'lft'); ?>
		<?php echo $form->textField($model, 'lft'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'rgt'); ?>
		<?php echo $form->textField($model, 'rgt'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'level'); ?>
		<?php echo $form->textField($model, 'level'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 64)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'deleted'); ?>
		<?php echo $form->dropDownList($model, 'deleted', array('0' => Yii::t('app', 'No'), '1' => Yii::t('app', 'Yes')), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
