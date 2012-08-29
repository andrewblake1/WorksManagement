<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');

	ProjectTypeToAuthItemController::listWidgetRow($model, $form, 'project_type_to_AuthItem_id');

$this->endWidget();

?>
