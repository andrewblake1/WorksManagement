<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set standard id in controller
	$materialGroup = MaterialGroup::model()->findByPk($model->material_group_id);
	MaterialController::listWidgetRow($model, $form, 'material_id', array(),
		array('scopeStandard'=>array($materialGroup->standard_id)));

$this->endWidget();

?>