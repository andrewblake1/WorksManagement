<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	TaskTypeController::listWidgetRow($model, $form, 'task_type_id');

	GenericTaskCategoryController::listWidgetRow($model, $form, 'generic_task_category_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
