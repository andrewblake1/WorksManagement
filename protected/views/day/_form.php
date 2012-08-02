<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'scheduled',array('class'=>'span5'));

	echo $form->textFieldRow($model,'preferred',array('class'=>'span5'));

	echo $form->textFieldRow($model,'earliest',array('class'=>'span5'));

	echo $form->textFieldRow($model,'planned',array('class'=>'span5'));

$this->endWidget();

?>
