<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'task_id');

	GenericTaskTypeController::listWidgetRow($model, $form, 'generic_task_type_id');

	echo $form->textFieldRow($model,'generic_id',array('class'=>'span5','maxlength'=>10));

$this->endWidget();

?>
