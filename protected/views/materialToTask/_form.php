<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	echo $form->textFieldRow($model,'task_id',array('class'=>'span5','maxlength'=>10));

	echo $form->textFieldRow($model,'quantity',array('class'=>'span5'));

$this->endWidget();

?>
