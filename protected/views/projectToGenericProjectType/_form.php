<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if($model->isNewRecord)
	{
		GenericProjectTypeController::listWidgetRow($model, $form, 'generic_project_type_id');
//		$form->textFieldRow('generic_id');
	}
	else
	{
		$this->widget('GenericWidget', array(
			'form'=>$form,
			'relation_modelToGenericModelType'=>'taskToGenericProjectType',
			'toGenericType'=>$model,
			'relation_genericModelType'=>'genericProjectType',
		));
	}


$this->endWidget();

?>
