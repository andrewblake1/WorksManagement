<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$project = Project::model()->findByPk($model->project_id);
	ClientContactController::listWidgetRow($model, $form, 'client_contact_id', array(), array('scopeClient'=>array($project->client_id)));

$this->endWidget();

?>