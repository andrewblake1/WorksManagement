<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>255/*, 'disabled'=>'disabled'*/));

	echo $form->textFieldRow($model,'url',array('class'=>'span5','maxlength'=>255));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	echo $form->textFieldRow($model,'quantity',array('class'=>'span5'));

$this->endWidget();

?>
