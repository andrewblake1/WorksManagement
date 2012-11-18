<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	if($model->isNewRecord)
	{
		GenericProjectTypeController::listWidgetRow($model, $form, 'generic_project_type_id');
//		$form->textFieldRow('generic_id');
	}
	else
	{
		$this->widget('GenericWidget', array(
			'form'=>$form,
			'generic'=>$model->generic,
			'genericType'=>$model->genericProjectType->genericType,
			'relationToGenericType'=>'projectToGenericProjectType->genericProjectType->genericType'
		));
/*		$this->widget('GenericWidget', array(
			'form'=>$form,
			'relation_modelToGenericModelType'=>'taskToGenericProjectType',
			'toGenericType'=>$model,
			'relation_genericModelType'=>'genericProjectType',
		));*/
	}

$this->endWidget();

?>