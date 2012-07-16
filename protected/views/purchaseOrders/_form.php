<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'purchase-orders-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'supplier_id'); ?>
		<?php echo $form->dropDownList($model, 'supplier_id', GxHtml::listDataEx(Supplier::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'supplier_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'purchase_order_no'); ?>
		<?php echo $form->textField($model, 'purchase_order_no', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'purchase_order_no'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('tasks')); ?></label>
		<?php echo $form->checkBoxList($model, 'tasks', GxHtml::encodeEx(GxHtml::listDataEx(Task::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->