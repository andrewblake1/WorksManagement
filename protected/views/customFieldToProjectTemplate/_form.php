<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	CustomFieldProjectCategoryController::listWidgetRow($model, $form, 'custom_field_project_category_id');

	CustomFieldController::listWidgetRow($model, $form, 'custom_field_id');

$this->endWidget();

?>