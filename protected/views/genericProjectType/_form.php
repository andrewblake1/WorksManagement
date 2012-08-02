<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

	GenericProjectCategoryController::listWidgetRow($model, $form, 'generic_project_category_id');

$this->endWidget();

?>
