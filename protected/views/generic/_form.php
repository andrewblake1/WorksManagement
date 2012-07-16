<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'generic-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'type_int'); ?>
		<?php echo $form->textField($model, 'type_int'); ?>
		<?php echo $form->error($model,'type_int'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'type_float'); ?>
		<?php echo $form->textField($model, 'type_float'); ?>
		<?php echo $form->error($model,'type_float'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'type_time'); ?>
		<?php echo $form->textField($model, 'type_time'); ?>
		<?php echo $form->error($model,'type_time'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'type_date'); ?>
		<?php $form->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'type_date',
			'value' => $model->type_date,
			'options' => array(
				'showButtonPanel' => true,
				'changeYear' => true,
				'dateFormat' => 'yy-mm-dd',
				),
			));
; ?>
		<?php echo $form->error($model,'type_date'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'type_text'); ?>
		<?php echo $form->textField($model, 'type_text', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'type_text'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'taskToGenericTaskTypes', GxHtml::encodeEx(GxHtml::listDataEx(TaskToGenericTaskType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->