<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'task-type-form',
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
		<?php echo $form->labelEx($model,'deleted'); ?>
		<?php echo $form->checkBox($model, 'deleted'); ?>
		<?php echo $form->error($model,'deleted'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'template_task_id'); ?>
		<?php echo $form->dropDownList($model, 'template_task_id', GxHtml::listDataEx(Task::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'template_task_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('clients')); ?></label>
		<?php echo $form->checkBoxList($model, 'clients', GxHtml::encodeEx(GxHtml::listDataEx(Client::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->