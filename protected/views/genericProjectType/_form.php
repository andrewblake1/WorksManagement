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

	GenericprojectcategoryController::listWidgetRow($model, $form, 'genericprojectcategory_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
