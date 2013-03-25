<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set store id in controller
	$assembly = Assembly::model()->findByPk($model->assembly_id);
	StandardDrawingController::listWidgetRow($model, $form, 'standard_drawing_id', array(),
		array('scopeStore'=>array($assembly->store_id)));

$this->endWidget();

?>