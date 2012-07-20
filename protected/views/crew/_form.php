<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'crew-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'preferred_date',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'earliest_date',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'date_scheduled',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'in_charge',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
