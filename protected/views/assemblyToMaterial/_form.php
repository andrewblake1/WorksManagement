<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->assembly_id))
	{
		$form->hiddenField('assembly_id');
	}	
	else
	{
		throw new CHttpException(400, 'No assembly identified, you must get here from the assemblys page');
	}

	MaterialController::listWidgetRow($model, $form, 'material_id');

	$form->textFieldRow('quantity');

$this->endWidget();

?>