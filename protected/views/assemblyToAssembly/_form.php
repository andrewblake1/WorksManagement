<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AssemblyController::listWidgetRow($model, $form, 'child_assembly_id');

	$form->textFieldRow('quantity');
	
$this->endWidget();

?>
