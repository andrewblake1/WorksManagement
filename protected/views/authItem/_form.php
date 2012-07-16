<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'auth-item-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'name'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model, 'type'); ?>
		<?php echo $form->error($model,'type'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'bizrule'); ?>
		<?php echo $form->textArea($model, 'bizrule'); ?>
		<?php echo $form->error($model,'bizrule'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'data'); ?>
		<?php echo $form->textArea($model, 'data'); ?>
		<?php echo $form->error($model,'data'); ?>
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

		<label><?php echo GxHtml::encode($model->getRelationLabel('authAssignments')); ?></label>
		<?php echo $form->checkBoxList($model, 'authAssignments', GxHtml::encodeEx(GxHtml::listDataEx(AuthAssignment::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('authItemchildren')); ?></label>
		<?php echo $form->checkBoxList($model, 'authItemchildren', GxHtml::encodeEx(GxHtml::listDataEx(AuthItemChild::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('authItemchildren1')); ?></label>
		<?php echo $form->checkBoxList($model, 'authItemchildren1', GxHtml::encodeEx(GxHtml::listDataEx(AuthItemChild::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('clientToTaskTypeToDutyTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'clientToTaskTypeToDutyTypes', GxHtml::encodeEx(GxHtml::listDataEx(ClientToTaskTypeToDutyType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->