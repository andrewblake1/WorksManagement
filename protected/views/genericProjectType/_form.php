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

	GenericprojectcategoryController::listWidgetRow($model, $form, 'genericprojectcategory_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>