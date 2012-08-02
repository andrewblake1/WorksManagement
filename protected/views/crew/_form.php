<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'preferred_date',array('class'=>'span5'));

	echo $form->textFieldRow($model,'earliest_date',array('class'=>'span5'));

	echo $form->textFieldRow($model,'date_scheduled',array('class'=>'span5'));

	StaffController::listWidgetRow($model, $form, 'in_charge_id');

$this->endWidget();

?>
