<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// get the parent model
	$assembly = Assembly::model()->findByPk(Controller::$nav['update']['Assembly']);
	AssemblyController::listWidgetRow($model, $form, 'child_assembly_id', array(),
		array('scopeStore'=>array($assembly->store_id)));

	$form->textFieldRow('quantity');
	
	$form->textAreaRow('comment');

$this->endWidget();

?>