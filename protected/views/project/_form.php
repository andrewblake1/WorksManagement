<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model, 'models'=>$models));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>255));
	
	ProjectTypeController::listWidgetRow($model, $form, 'project_type_id');

	echo $form->textFieldRow($model,'travel_time_1_way',array('class'=>'span5'));

	echo $form->textFieldRow($model,'critical_completion',array('class'=>'span5'));

	echo $form->textFieldRow($model,'planned',array('class'=>'span5'));

$this->endWidget();

?>
