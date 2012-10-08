<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	CHtml::resolveNameID($model, $attribute = 'assembly_id', $htmlOptions);
	SupplierController::listWidgetRow($model, $form, 'supplier_id',
		array(
			'empty'=>'Please select',
			'ajax' => array(
			'type'=>'POST',
			'url'=>CController::createUrl('Supplier/dynamicAssemblys'),
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
						supplier_id = $('#{$this->modelName}_supplier_id').val();
						lookup.autocomplete({'minLength':1,'maxHeight':'100','select':function(event, ui){"."$('#{$htmlOptions['id']}').val(ui.item.id);$('#{$htmlOptions['id']}_save').val(ui.item.value);},'source':'/WorksManagement/Assembly/autocomplete?model={$this->modelName}&attribute=assembly_id&{$this->modelName}%5Btask_id%5D=35&scopes%5BscopeSupplier%5D%5B0%5D=' + supplier_id});
					}
				}
			}",
		)),
		array(),
		'Supplier');

	// NB: need to set this here as otherwise in wmfkautocomplete the soure url has supplier_id=, in it which gets stripped
	if($model->supplier_id === null)
	{
		$model->supplier_id = 0;
	}
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array(), array('scopeSupplier'=>array($model->supplier_id)));

	$form->textFieldRow('quantity');

$this->endWidget();

?>

