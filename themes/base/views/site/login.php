<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->widget('bootstrap.widgets.BootBreadcrumbs', array(
    'links'=>array('Login'),
));
?>

<h1>Login</h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="well">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
		'errorMessageCssClass'=>'alert alert-error',
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model,NULL,NULL,$htmlOptions=array('class'=>'alert alert-error')); ?>
	<fieldset>
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>

		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
<!--		<p class="alert alert-info">
			Hint: You may login with <tt>demo/demo</tt> or <tt>admin/admin</tt>.
		</p>-->

		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>

		<div class="form-actions">
			<?php echo CHtml::submitButton('Login', array('class'=>'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div><!-- well -->