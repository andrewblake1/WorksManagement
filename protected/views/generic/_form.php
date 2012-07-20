<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'generic-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'type_int',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_float',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_time',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_date',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_text',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
