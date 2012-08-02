<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>255));
	
	echo $form->textFieldRow($model,'travel_time_1_way',array('class'=>'span5'));

	echo $form->textFieldRow($model,'critical_completion',array('class'=>'span5'));

	echo $form->textFieldRow($model,'planned',array('class'=>'span5'));

	ClientController::listWidgetRow($model, $form, 'client_id');

$this->endWidget();

?>
