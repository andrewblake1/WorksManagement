<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	resourceCategoryController::listWidgetRow($model, $form, 'resource_category_id');

	$form->textFieldRow('maximum');

$this->endWidget();

?>
