<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'itemname',array('class'=>'span5','maxlength'=>64));

	StaffController::listWidgetRow($model, $form, 'userid');

	echo $form->textAreaRow($model,'bizrule',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textAreaRow($model,'data',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

$this->endWidget();

?>
