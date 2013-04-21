<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	AssemblyController::dependantListWidgetRow($model, $form, 'assembly_id', 'Store', 'store_id', array(), array('scopeStore'=>array($model->store_id === null ? 0 : $model->store_id)));

	// if sub assembly
	if(!$model->isNewRecord && $model->parent_id)
	{
		// parent id in sub_assembly table
		$parent_assembly_id = $model->parent->assembly_id;
		// child id in sub_assembly table
		$child_assembly_id = $model->assembly_id;
		$subAssembly = SubAssembly::model()->findByAttributes(array('child_assembly_id'=>$child_assembly_id, 'parent_assembly_id'=>$parent_assembly_id));
		$form->rangeFieldRow('quantity', $subAssembly->minimum, $subAssembly->maximum, $subAssembly->select, $subAssembly->quantity_tooltip);
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

