<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AssemblyController::listWidgetRow($model, $form, 'material_id');
	$form->textFieldRow('alias');

$this->endWidget();

?>