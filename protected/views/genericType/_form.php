<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'generic-type-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'label'); ?>
		<?php echo $form->textField($model, 'label', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'label'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'mandatory'); ?>
		<?php echo $form->textField($model, 'mandatory', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'mandatory'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'allow_new'); ?>
		<?php echo $form->checkBox($model, 'allow_new'); ?>
		<?php echo $form->error($model,'allow_new'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'validation_type_id'); ?>
		<?php echo $form->textField($model, 'validation_type_id', array('maxlength' => 10)); ?>
		<?php echo $form->error($model,'validation_type_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'data_type'); ?>
		<?php echo $form->textArea($model, 'data_type'); ?>
		<?php echo $form->error($model,'data_type'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('dutyTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'dutyTypes', GxHtml::encodeEx(GxHtml::listDataEx(DutyType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericProjectTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericProjectTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericProjectType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericTaskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericTaskTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericTaskType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->