<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->project_type_id))
	{
		$form->hiddenField('project_type_id');
	}
	else
	{
		ProjectTypeController::listWidgetRow($model, $form, 'project_type_id');
	}
	
	AuthItemController::listWidgetRow($model, $form, 'AuthItem_name');

$this->endWidget();

?>