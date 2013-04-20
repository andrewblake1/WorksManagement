<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	MaterialController::dependantListWidgetRow($model, $form, 'material_id', 'Store', 'store_id', array(), array('scopeStore'=>array($model->store_id === null ? 0 : $model->store_id)));
	
	$form->textFieldRow('quantity');

$this->endWidget();

?>
