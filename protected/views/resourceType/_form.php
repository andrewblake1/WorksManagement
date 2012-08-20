<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	// if no resourcecategory_id known or we are updating 
	if(!isset($model->resourcecategory_id) || !$model->isNewRecord)
	{
		resourcecategoryController::listWidgetRow($model, $form, 'resourcecategory_id');
	}
	else
	{
		$form->hiddenField('resourcecategory_id');
	}	

	$form->textFieldRow('maximum');

$this->endWidget();

?>
