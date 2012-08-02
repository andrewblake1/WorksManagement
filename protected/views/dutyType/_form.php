<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'lead_in_days',array('class'=>'span5'));

	DutyCategoryController::listWidgetRow($model, $form, 'duty_category_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
