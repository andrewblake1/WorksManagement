<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'resource-type-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model, 'description', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'resource_category_id'); ?>
		<?php echo $form->dropDownList($model, 'resource_category_id', GxHtml::listDataEx(Resourcecategory::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'resource_category_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'maximum'); ?>
		<?php echo $form->textField($model, 'maximum'); ?>
		<?php echo $form->error($model,'maximum'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'deleted'); ?>
		<?php echo $form->checkBox($model, 'deleted'); ?>
		<?php echo $form->error($model,'deleted'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('tasks')); ?></label>
		<?php echo $form->checkBoxList($model, 'tasks', GxHtml::encodeEx(GxHtml::listDataEx(Task::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->