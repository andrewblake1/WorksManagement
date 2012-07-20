<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'day-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'scheduled',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'preferred',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'earliest',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'planned',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
