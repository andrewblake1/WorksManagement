<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	GenericProjectTypeController::listWidgetRow($model, $form, 'generic_project_type_id');

	ProjectController::listWidgetRow($model, $form, 'project_id');

	echo $form->textFieldRow($model,'generic_id',array('class'=>'span5','maxlength'=>255));

$this->endWidget();

?>
