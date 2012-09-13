<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('name');

	StaffController::listWidgetRow($model->id0 ? $model->id0 : new Schedule, $form, 'in_charge_id', array(), array(), 'In charge');

$this->endWidget();

?>