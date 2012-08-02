<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	echo $form->textFieldRow($model,'quantity',array('class'=>'span5'));

	echo $form->textFieldRow($model,'hours',array('class'=>'span5'));

	echo $form->textFieldRow($model,'start',array('class'=>'span5'));

$this->endWidget();

?>
