<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	StandardDrawingController::listWidgetRow($model, $form, 'standard_drawing_id');

$this->endWidget();

?>