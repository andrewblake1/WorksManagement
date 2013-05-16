<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set standard id in controller
	$assemblyGroup = AssemblyGroup::model()->findByPk($model->assembly_group_id);
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array(),
		array('scopeStandard'=>array($assemblyGroup->standard_id)));

$this->endWidget();

?>