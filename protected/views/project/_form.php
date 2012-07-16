<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'project-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'travel_time_1_way'); ?>
		<?php echo $form->textField($model, 'travel_time_1_way'); ?>
		<?php echo $form->error($model,'travel_time_1_way'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'critical_completion'); ?>
		<?php $form->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'critical_completion',
			'value' => $model->critical_completion,
			'options' => array(
				'showButtonPanel' => true,
				'changeYear' => true,
				'dateFormat' => 'yy-mm-dd',
				),
			));
; ?>
		<?php echo $form->error($model,'critical_completion'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'planned'); ?>
		<?php $form->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'planned',
			'value' => $model->planned,
			'options' => array(
				'showButtonPanel' => true,
				'changeYear' => true,
				'dateFormat' => 'yy-mm-dd',
				),
			));
; ?>
		<?php echo $form->error($model,'planned'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
		<?php echo $form->dropDownList($model, 'client_id', GxHtml::listDataEx(Client::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'client_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('authAssignments')); ?></label>
		<?php echo $form->checkBoxList($model, 'authAssignments', GxHtml::encodeEx(GxHtml::listDataEx(AuthAssignment::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericProjectTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericProjectTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericProjectType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('tasks')); ?></label>
		<?php echo $form->checkBoxList($model, 'tasks', GxHtml::encodeEx(GxHtml::listDataEx(Task::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->