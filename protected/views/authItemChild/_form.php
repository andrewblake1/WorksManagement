<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

/*	// set scope to limit to roles - applies here when list box but in autocomplete when drop down
// todo: alias might be relation for the attribute
	AuthItemController::listWidgetRow($model, $form, 'parent', array(), array('condition'=>'parent0.type=' . AuthItem::typeRole));

	// set scope to limit to rights
	AuthItemController::listWidgetRow($model, $form, 'child', array(), array('condition'=>'child0.type=' . AuthItem::typeRight));*/

	// set scope to limit to roles - applies here when list box but in autocomplete when drop down
//	AuthItemController::listWidgetRow($model, $form, 'parent', array(), array('roles'));
	if(isset($model->parent))
	{
		$form->hiddenField('parent');
	}
	else
	{
		throw new CHttpException(400, 'No role identified, you must get here from the roles page');
	}

	// set scope to limit to rights
	AuthItemController::listWidgetRow($model, $form, 'child', array(), array('rights'), 'Priveledge');

$this->endWidget();

?>