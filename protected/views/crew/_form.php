<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
//	'models'=>$models,
	'action'=>empty($action) ? null : $action, 
//	'parent_fk'=>$parent_fk,
	'htmlOptions'=>Yii::app()->user->checkAccess('scheduler') ? array('readonly'=>'readonly') : array(),
));

	StaffController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');

$this->endWidget();

?>