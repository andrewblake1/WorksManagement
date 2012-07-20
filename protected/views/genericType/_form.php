<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'generic-type-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'label',array('class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'mandatory',array('class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'allow_new',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'validation_type_id',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textAreaRow($model,'data_type',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
