<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('alias');

	DrawingController::listWidgetRow($model, $form, 'drawing_id', array(), array('scopeStandard'=>array($model->standard_id)), 'Drawing');
	
$this->endWidget();

?>