<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'generictaskcategory-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'root'); ?>
		<?php echo $form->textField($model, 'root'); ?>
		<?php echo $form->error($model,'root'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'lft'); ?>
		<?php echo $form->textField($model, 'lft'); ?>
		<?php echo $form->error($model,'lft'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'rgt'); ?>
		<?php echo $form->textField($model, 'rgt'); ?>
		<?php echo $form->error($model,'rgt'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'level'); ?>
		<?php echo $form->textField($model, 'level'); ?>
		<?php echo $form->error($model,'level'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'name'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'deleted'); ?>
		<?php echo $form->checkBox($model, 'deleted'); ?>
		<?php echo $form->error($model,'deleted'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('genericTaskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericTaskTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericTaskType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->