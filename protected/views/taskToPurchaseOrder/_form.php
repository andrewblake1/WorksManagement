<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-to-purchase-order-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'task_id'); ?>
		<?php echo $form->textField($model,'task_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'task_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'purchase_order_id'); ?>
		<?php echo $form->textField($model,'purchase_order_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'purchase_order_id'); ?>
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