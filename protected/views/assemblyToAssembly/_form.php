<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AssemblyController::listWidgetRow($model, $form, 'child_assembly_id', array(),
		array('scopeStore'=>array($model->parentAssembly->store_id)));

	$form->textFieldRow('quantity');
	
	$form->textAreaRow('comment');

$this->endWidget();

?>