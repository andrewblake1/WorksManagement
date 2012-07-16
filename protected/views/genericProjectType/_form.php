<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'generic-project-type-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'generic_type_id'); ?>
		<?php echo $form->dropDownList($model, 'generic_type_id', GxHtml::listDataEx(GenericType::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'generic_type_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'generic_project_category_id'); ?>
		<?php echo $form->dropDownList($model, 'generic_project_category_id', GxHtml::listDataEx(Genericprojectcategory::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'generic_project_category_id'); ?>
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

		<label><?php echo GxHtml::encode($model->getRelationLabel('projects')); ?></label>
		<?php echo $form->checkBoxList($model, 'projects', GxHtml::encodeEx(GxHtml::listDataEx(Project::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->