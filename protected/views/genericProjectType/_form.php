<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	GenericprojectcategoryController::listWidgetRow($model, $form, 'genericprojectcategory_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>