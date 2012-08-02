<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	ClientToTaskTypeController::listWidgetRow($model, $form, 'client_to_task_type_id');

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	GenericTaskCategoryController::listWidgetRow($model, $form, 'generic_task_category_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
