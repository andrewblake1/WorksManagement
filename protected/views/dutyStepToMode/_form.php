<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>