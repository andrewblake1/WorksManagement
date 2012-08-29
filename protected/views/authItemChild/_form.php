<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	// set scope to limit to rights
	AuthItemController::listWidgetRow($model, $form, 'child', array(), array('rights'), 'Priveledge');

$this->endWidget();

?>