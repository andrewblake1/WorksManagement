<?php

$form=$this->beginWidget('WMTbFileUploadActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');
	
	$form->textFieldRow('alias');
	
	if($this->checkAccess(Controller::accessWrite))
	{
		$form->textFieldRow('default_order');
	}
	
	// parent_id
	if($this->checkAccess(Controller::accessWrite))
	{
		if($model->isNewRecord)
		{
			$form->hiddenField('parent_id');
		}
		else
		{
			static::listWidgetRow($model, $form, 'parent_id', array(), array('scopeStandard'=>array($model->standard_id)), 'Parent');
		}
	}
		
$this->endWidget();
?>