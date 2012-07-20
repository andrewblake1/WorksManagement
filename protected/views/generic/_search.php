<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'type_int',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_float',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_time',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_date',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'type_text',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'staff_id',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
