<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set store id in controller
	$assemblyGroup = AssemblyGroup::model()->findByPk($model->assembly_group_id);
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array(),
		array('scopeStore'=>array($assemblyGroup->store_id)));

$this->endWidget();

?>