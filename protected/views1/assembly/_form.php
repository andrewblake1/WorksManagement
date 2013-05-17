<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('alias');
	
	// parent_id
	if($this->checkAccess(Controller::accessWrite))
	{
		static::listWidgetRow($model, $form, 'parent_id', array(), array('scopeStandard'=>array($model->standard_id)), 'Parent');
	}
	
$this->endWidget();

?>