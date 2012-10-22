<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	StandardDrawingController::listWidgetRow($model, $form, 'standard_drawing_id', array(),
		array('scopeAssembly'=>array($model->assembly_id)));

$this->endWidget();

?>