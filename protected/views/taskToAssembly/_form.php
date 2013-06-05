<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	AssemblyController::dependantListWidgetRow($model, $form, 'assembly_id', 'Standard', 'standard_id', array(), array('scopeStandard'=>array($model->standard_id === null ? 0 : $model->standard_id)));

	// if sub assembly
	if(!$model->isNewRecord && $model->parent_id)
	{
		$subAssembly = $model->subAssembly;
		$form->rangeFieldRow('quantity', $subAssembly->quantity, $subAssembly->minimum, $subAssembly->maximum, $subAssembly->select, $subAssembly->quantity_tooltip);
	}
	else
	{
		$form->textFieldRow('quantity');
	}

	// parent_id
	if($this->checkAccess(Controller::accessWrite))
	{
		static::listWidgetRow($model, $form, 'parent_id', array(), array(), 'Parent');
	}

$this->endWidget();

?>

