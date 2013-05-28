<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	if(isset($_GET['project_template_id']))
	{
		self::listWidgetRow($model, $form, 'override_id', array(), array('scopeProject'=>array($model->project_template_id)));
	}
	elseif(isset($_GET['client_id']))
	{
		self::listWidgetRow($model, $form, 'override_id', array(), array('scopeClient'=>array($model->client_id)));
	}

$this->endWidget();

?>