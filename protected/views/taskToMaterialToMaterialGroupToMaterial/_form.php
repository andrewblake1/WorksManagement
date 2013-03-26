<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-to-material-to-material-group-to-material-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'task_to_material_id'); ?>
		<?php echo $form->textField($model,'task_to_material_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'task_to_material_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'material_group_id'); ?>
		<?php echo $form->textField($model,'material_group_id'); ?>
		<?php echo $form->error($model,'material_group_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'material_id'); ?>
		<?php echo $form->textField($model,'material_id'); ?>
		<?php echo $form->error($model,'material_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->textField($model,'staff_id'); ?>
		<?php echo $form->error($model,'staff_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->