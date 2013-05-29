<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	// if a project template is given
	if(isset($_GET['project_template_id']))
	{
		// scope to exclude all project template level as well non current client
		static::listWidgetRow($model, $form, 'override_id', array(), array('scopeProjectTemplate'=>array($model->project_template_id)));
	}
	// otherwise if a client is given
	elseif(isset($_GET['client_id']))
	{
		// scope to only allow where both project template and client are null
		static::listWidgetRow($model, $form, 'override_id', array(), array('scopeClient'), 'Replace');
	}

$this->endWidget();

?>