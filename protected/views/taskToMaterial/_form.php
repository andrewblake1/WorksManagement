<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	CHtml::resolveNameID($model, $attribute = 'material_id', $htmlOptions);
	
	$source = Yii::app()->createUrl("Material/autocomplete") . "?model={$this->modelName}&attribute=material_id&scopes%5BscopeStore%5D%5B0%5D=";

	StoreController::listWidgetRow($model, $form, 'store_id',
		array(
			'empty'=>'Please select',
			'ajax' => array(
			'type'=>'POST',
			'url'=>CController::createUrl('Store/dynamicMaterials'),
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
	if($model->store_id === NULL)
	{
		$model->store_id = 0;
	}
	MaterialController::listWidgetRow($model, $form, 'material_id', array(), array('scopeStore'=>array($model->store_id)));
	
	// get quantity tooltip if part of assembly
	if(!empty($model->taskToMaterialToAssemblyToMaterials))
	{
		// there ia a unique constraint here so there will only be 1 relating row
		$quantity_tooltip = $model->taskToMaterialToAssemblyToMaterials[0]->assemblyToMaterial->quantity_tooltip;
	}

	$form->textFieldRow('quantity', array('data-original-title'=>$quantity_tooltip));

$this->endWidget();

?>
