<?php
/* @var $this DutyStepDependencyController */
/* @var $model DutyStepDependency */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'duty-step-dependency-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'parent_duty_step_id'); ?>
		<?php echo $form->textField($model,'parent_duty_step_id'); ?>
		<?php echo $form->error($model,'parent_duty_step_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'child_duty_step_id'); ?>
		<?php echo $form->textField($model,'child_duty_step_id'); ?>
		<?php echo $form->error($model,'child_duty_step_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'duty_type_id'); ?>
		<?php echo $form->textField($model,'duty_type_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'duty_type_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deleted'); ?>
		<?php echo $form->textField($model,'deleted'); ?>
		<?php echo $form->error($model,'deleted'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'updated_by'); ?>
		<?php echo $form->textField($model,'updated_by'); ?>
		<?php echo $form->error($model,'updated_by'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->