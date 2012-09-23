<?php

// only editable by scheduler
$htmlOptions = Yii::app()->user->checkAccess('scheduler') ? array('readonly'=>'readonly') : array();

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'action'=>$action, 'parent_fk'=>$parent_fk, 'parent_fk'=>$parent_fk, 'htmlOptions'=>$htmlOptions));

	StaffController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');

$this->endWidget();

?>