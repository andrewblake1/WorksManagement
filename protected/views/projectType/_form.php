<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	// client_id should always be set unless come directly from url so cover this to be safe anyway
	if(isset($model->client_id))
	{
		$form->hiddenField('client_id');
	}
	else
	{
		throw new CHttpException(400, 'No client identified, you must get here from the clients page');
	}
	
/*
 * provisional

	ProjectController::listWidgetRow($model, $form, 'template_project_id', array(), array(), 'Template project');
*/

$this->endWidget();

?>	