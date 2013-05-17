<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set standard id in controller
	$assembly = Assembly::model()->findByPk($model->assembly_id);
	DrawingController::listWidgetRow($model, $form, 'drawing_id', array(),
		array('scopeStandard'=>array($assembly->standard_id)));

$this->endWidget();

?>