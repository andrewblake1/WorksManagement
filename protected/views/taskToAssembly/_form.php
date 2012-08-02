<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'task_id');

	AssemblyController::listWidgetRow($model, $form, 'assembly_id');

	echo $form->textFieldRow($model,'quantity',array('class'=>'span5'));

$this->endWidget();

?>
