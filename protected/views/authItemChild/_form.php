<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// set scope to limit to rights
	AuthItemController::listWidgetRow($model, $form, 'child', array(), array('rights'), 'Privilege');

$this->endWidget();

?>