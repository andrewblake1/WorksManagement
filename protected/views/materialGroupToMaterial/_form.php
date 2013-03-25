<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set store id in controller
	$materialGroup = MaterialGroup::model()->findByPk($model->material_group_id);
	MaterialController::listWidgetRow($model, $form, 'material_id', array(),
		array('scopeStore'=>array($materialGroup->store_id)));

$this->endWidget();

?>