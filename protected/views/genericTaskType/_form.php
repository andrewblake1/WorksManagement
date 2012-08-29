<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	GenerictaskcategoryController::listWidgetRow($model, $form, 'generictaskcategory_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
