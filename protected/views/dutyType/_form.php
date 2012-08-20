<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	$form->textFieldRow('lead_in_days');

	// if no dutycategory_id known or we are updating 
	if(!isset($model->dutycategory_id) || !$model->isNewRecord)
	{
		DutycategoryController::listWidgetRow($model, $form, 'dutycategory_id');
	}
	else
	{
		$form->hiddenField('dutycategory_id');
	}	

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
