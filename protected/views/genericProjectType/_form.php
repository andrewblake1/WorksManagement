<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	ProjectTypeController::listWidgetRow($model, $form, 'project_type_id');

	GenericProjectCategoryController::listWidgetRow($model, $form, 'generic_project_category_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
