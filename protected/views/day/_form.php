<?php
$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
//	'models'=>$models,
	'action'=>empty($action) ? null : $action, 
//	'parent_fk'=>$parent_fk,
	'htmlOptions'=>Yii::app()->user->checkAccess('scheduler') ? array('readonly'=>'readonly') : array(),
));

	$form->textFieldRow('name');

	StaffController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');

	// NB: show as read only unless scheduler rights
	$form->textFieldRow('scheduled', Yii::app()->user->checkAccess('scheduler') ? array() : array('disabled'=>'disabled'));
	
$this->endWidget();

?>