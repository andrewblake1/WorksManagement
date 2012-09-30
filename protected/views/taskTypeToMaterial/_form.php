<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	MaterialController::listWidgetRow($model, $form, 'material_id', array(), array(
		'scopeClient'=>array($this->modelName, $model->task_type_id),
	));

	$form->textFieldRow('quantity');

$this->endWidget();

?>