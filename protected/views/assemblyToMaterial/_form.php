<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	MaterialController::listWidgetRow($model, $form, 'material_id', array(),
		array('scopeAssembly'=>array($model->assembly_id)));

	$form->textFieldRow('quantity');

$this->endWidget();

?>