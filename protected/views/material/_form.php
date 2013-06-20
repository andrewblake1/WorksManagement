<?php

$form=$this->beginWidget('WMTbFileUploadActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('alias');

	$form->textAreaRow('category');

	DrawingController::listWidgetRow($model, $form, 'drawing_id', array(), array('scopeStandard'=>array($model->standard_id)), 'Drawing');

	$form->textFieldRow('unit');

$this->endWidget();

?>