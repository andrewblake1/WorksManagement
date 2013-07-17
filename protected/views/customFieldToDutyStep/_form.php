<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

//	CustomFieldDutyStepCategoryController::listWidgetRow($model, $form, 'custom_field_duty_step_category_id');

	CustomFieldController::listWidgetRow($model, $form, 'custom_field_id');

$this->endWidget();

?>