<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'type_int',array('class'=>'span5'));

	echo $form->textFieldRow($model,'type_float',array('class'=>'span5'));

	echo $form->textFieldRow($model,'type_time',array('class'=>'span5'));

	echo $form->textFieldRow($model,'type_date',array('class'=>'span5'));

	echo $form->textFieldRow($model,'type_text',array('class'=>'span5','maxlength'=>255));

$this->endWidget();

?>
