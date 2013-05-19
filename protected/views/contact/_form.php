<?php
/* @var $this ContactController */
/* @var $model Contact */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contact-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'first_name'); ?>
		<?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'first_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_name'); ?>
		<?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'last_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'address_line_1'); ?>
		<?php echo $form->textField($model,'address_line_1',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'address_line_1'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'address_line_2'); ?>
		<?php echo $form->textField($model,'address_line_2',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'address_line_2'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'post_code'); ?>
		<?php echo $form->textField($model,'post_code',array('size'=>16,'maxlength'=>16)); ?>
		<?php echo $form->error($model,'post_code'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'town_city'); ?>
		<?php echo $form->textField($model,'town_city',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'town_city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'state_province'); ?>
		<?php echo $form->textField($model,'state_province',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'state_province'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'country'); ?>
		<?php echo $form->textField($model,'country',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'country'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone_mobile'); ?>
		<?php echo $form->textField($model,'phone_mobile',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'phone_mobile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone_home'); ?>
		<?php echo $form->textField($model,'phone_home',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'phone_home'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone_work'); ?>
		<?php echo $form->textField($model,'phone_work',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'phone_work'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone_fax'); ?>
		<?php echo $form->textField($model,'phone_fax',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'phone_fax'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deleted'); ?>
		<?php echo $form->textField($model,'deleted'); ?>
		<?php echo $form->error($model,'deleted'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tbl_user_id'); ?>
		<?php echo $form->textField($model,'tbl_user_id'); ?>
		<?php echo $form->error($model,'tbl_user_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->