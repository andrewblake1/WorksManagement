<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<?php echo $form->textFieldRow($model,'day',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'purchase_orders_id',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'crew_id',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'project_id',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'client_to_task_type_client_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'client_to_task_type_task_type_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
