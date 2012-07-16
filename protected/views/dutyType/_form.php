<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'duty-type-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model, 'description', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'lead_in_days'); ?>
		<?php echo $form->textField($model, 'lead_in_days'); ?>
		<?php echo $form->error($model,'lead_in_days'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'duty_category_id'); ?>
		<?php echo $form->dropDownList($model, 'duty_category_id', GxHtml::listDataEx(Dutycategory::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'duty_category_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'generic_type_id'); ?>
		<?php echo $form->dropDownList($model, 'generic_type_id', GxHtml::listDataEx(GenericType::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'generic_type_id'); ?>
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

		<label><?php echo GxHtml::encode($model->getRelationLabel('clientToTaskTypeToDutyTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'clientToTaskTypeToDutyTypes', GxHtml::encodeEx(GxHtml::listDataEx(ClientToTaskTypeToDutyType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->