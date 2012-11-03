<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	CHtml::resolveNameID($model, $attribute = 'column', $htmlOptions);
	
	$source = Yii::app()->createUrl("{$this->modelName}/autocomplete") . "?attribute=column&table=";

	$afterSelect = 
		CHtml::ajax(array(
			'type'=>'POST',
			'url'=>CController::createUrl("{$this->modelName}/dynamicColumns"),
			'success'=>"function(data) {
				if(data)
				{
					$('[for=\"{$htmlOptions['id']}\"]').remove();
					$('#{$htmlOptions['id']}_save').remove();
					$('#{$htmlOptions['id']}_em_').remove();
					$('#{$htmlOptions['id']}_lookup').remove();
					$('#{$htmlOptions['id']}').replaceWith(data);
					// if this is autotext
					lookup = $('#{$htmlOptions['id']}_lookup');
					if(lookup.length)
					{
						table = $('#{$this->modelName}_table').val();
						lookup.autocomplete({'minLength':1,'maxHeight':'100','select':function(event, ui){"."$('#{$htmlOptions['id']}').val(ui.item.id);$('#{$htmlOptions['id']}_save').val(ui.item.value);},'source':'$source' + table});
					}
				}
			}"
		));		
		
	$this->widget('WMEJuiAutoCompleteTable', array(
		'model'=>$model,
		'form'=>$form,
		'afterSelect'=>$afterSelect,
	));


	// NB: need to set this here as otherwise in wmfkautocomplete the soure url has table=, in it which gets stripped
//	if($model->table === null)
//	{
//		$model->table = 0;
//	}

	echo $form->dropDownListRow(
		'column',
		$this->getColumns($model->table),
		array(),
		$model
	);

	$form->textAreaRow('select');

$this->endWidget();

?>