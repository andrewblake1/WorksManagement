<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'action'=>$action, 'parent_fk'=>$parent_fk));

	// only allow setting or update of in_charge_id if user has InCharge priveledge
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		$form->textFieldRow('name');

		StaffController::listWidgetRow($model->id0 ? $model->id0 : new Schedule, $form, 'in_charge_id', array(), array(), 'In charge');
	}

$this->endWidget();

?>