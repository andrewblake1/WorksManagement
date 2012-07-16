<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'id'); ?>
		<?php echo $form->textField($model, 'id', array('maxlength' => 10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'type_int'); ?>
		<?php echo $form->textField($model, 'type_int'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'type_float'); ?>
		<?php echo $form->textField($model, 'type_float'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'type_time'); ?>
		<?php echo $form->textField($model, 'type_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'type_date'); ?>
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
	</div>

	<div class="row">
		<?php echo $form->label($model, 'type_text'); ?>
		<?php echo $form->textField($model, 'type_text', array('maxlength' => 255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
