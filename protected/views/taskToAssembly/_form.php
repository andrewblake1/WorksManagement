<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	CHtml::resolveNameID($model, $attribute = 'assembly_id', $htmlOptions);

	$source = Yii::app()->createUrl("Assembly/autocomplete") . "?model={$this->modelName}&attribute=assembly_id&scopes%5BscopeStore%5D%5B0%5D=";

	StoreController::listWidgetRow($model, $form, 'store_id',
		array(
			'empty'=>'Please select',
			'ajax' => array(
			'type'=>'POST',
			'url'=>CController::createUrl('Store/dynamicAssemblies'),
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
						store_id = $('#{$this->modelName}_store_id').val();
						lookup.autocomplete({'minLength':1,'maxHeight':'100','select':function(event, ui){"."$('#{$htmlOptions['id']}').val(ui.item.id);$('#{$htmlOptions['id']}_save').val(ui.item.value);},'source':'$source' + store_id});
					}
				}
			}",
		)),
		array(),
		'Store');

	// NB: need to set this here as otherwise in wmfkautocomplete the soure url has store_id=, in it which gets stripped
	if($model->store_id === null)
	{
		$model->store_id = 0;
	}
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array(), array('scopeStore'=>array($model->store_id)));

	if($model->isNewRecord)
	{
		$form->textFieldRow('quantity');
	}
	else
	{
		// dummy as compulsary when new but not required when updating
		$model->quantity = 1;
		$form->hiddenField('quantity');
	}

$this->endWidget();

?>

