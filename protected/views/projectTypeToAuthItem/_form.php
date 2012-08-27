<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->project_type_id))
	{
		$form->hiddenField('project_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No project identified, you must get here from the projects page');
	}
	
	AuthItemController::listWidgetRow($model, $form, 'AuthItem_name', array(), array('roles'));

$this->endWidget();

?>