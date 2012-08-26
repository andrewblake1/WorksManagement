<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if($model->isNewRecord)
	{
		GenericProjectTypeController::listWidgetRow($model, $form, 'generic_project_type_id');

		if(isset($model->project_id))
		{
			$form->hiddenField('project_id');
		}
		else
		{
			ProjectController::listWidgetRow($model, $form, 'project_id');
		}

		$form->textFieldRow('generic_id');
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
