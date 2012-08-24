<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AuthItemController::listWidgetRow($model, $form, 'itemname', array(), array('roles'));
//	$form->textFieldRow('itemname');

	if(!isset($model->userid))
	{
		StaffController::listWidgetRow($model, $form, 'userid');
	}
	else
	{
		$form->hiddenField('userid');
	}	

	$form->hiddenField('bizrule');

	$form->hiddenField('data');

$this->endWidget();

?>
