<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('itemname');

	if(!isset($model->userid))
	{
		StaffController::listWidgetRow($model, $form, 'userid');
	}
	else
	{
		$form->hiddenField('userid');
	}	

	$form->textAreaRow('bizrule');

	$form->textAreaRow('data');

$this->endWidget();

?>
