<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

//	CustomFieldTaskCategoryController::listWidgetRow($model, $form, 'custom_field_task_category_id');

	CustomFieldController::listWidgetRow($model, $form, 'custom_field_id');

	$form->checkBoxRow('show_in_admin');	

	$form->checkBoxRow('show_in_planning');	

	$form->textFieldRow('label_override');

$this->endWidget();

?>
