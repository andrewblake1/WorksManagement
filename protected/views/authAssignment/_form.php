<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AuthItemController::listWidgetRow($model, $form, 'itemname', array(), array('roles'));
//	$form->textFieldRow('itemname');

	if(isset($model->userid))
	{
		$form->hiddenField('userid');
	}	
	else
	{
		throw new CHttpException(400, 'No user identified, you must get here from the staffs page');
	}

	$form->hiddenField('bizrule');

	$form->hiddenField('data');

$this->endWidget();

?>
