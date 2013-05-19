<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	// parent_id
	if($model->isNewRecord)
	{
		$form->hiddenField('parent_id');
	}
	else
	{
		static::listWidgetRow($model, $form, 'parent_id', array(), array(), 'Integral to');
	}

	$form->textFieldRow('lead_in_days');

	$form->dropDownListRow('level', Planning::$levels);

	CustomFieldController::listWidgetRow($model, $form, 'custom_field_id', array(), array(), null);

$this->endWidget();

?>