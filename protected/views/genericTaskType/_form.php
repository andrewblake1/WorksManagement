<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'generic-task-type-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'client_to_task_type_client_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'client_to_task_type_task_type_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'generic_task_category_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'generic_type_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'deleted',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
