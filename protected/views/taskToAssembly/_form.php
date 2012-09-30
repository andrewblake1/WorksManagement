<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array(), array(
		'scopeClient'=>array('Task', $model->task_id),
	));

	$form->textFieldRow('quantity');

$this->endWidget();

?>
