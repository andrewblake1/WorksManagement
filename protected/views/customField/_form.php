<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('label');

	$form->checkBoxRow('mandatory');	

	$htmlOptions = array();
	CHtml::resolveNameID($model, $temp = 'allow_new', $htmlOptions);
	$form->dropDownListRow('validation_type', $model->validationTypeLabels, array(
		// show allow new only if sql select
		'onchange'=>"
			if($(this).val() == '". CustomField::validation_typeSQLSelect . "')
			{
				$(\"[for='{$htmlOptions['id']}']\").show('slow');
			}
			else
			{
				$(\"[for='{$htmlOptions['id']}']\").hide('slow');
			};",
	));
	// trigger the change handler on document load
	$htmlOptions = array();
	CHtml::resolveNameID($model, $temp = 'validation_type', $htmlOptions);
	Yii::app()->clientScript->registerScript("validation_type", "
		$('select#{$htmlOptions['id']}').trigger('change');
		", CClientScript::POS_READY
	);

	$form->checkBoxRow('allow_new');
	
	$form->dropDownListRow('data_type', $model->dataTypeLabels);

	$form->textAreaRow('validation_text');

	$form->textAreaRow('validation_error');

$this->endWidget();

?>
