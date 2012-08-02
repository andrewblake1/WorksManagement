<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'root',array('class'=>'span5'));

	echo $form->textFieldRow($model,'lft',array('class'=>'span5'));

	echo $form->textFieldRow($model,'rgt',array('class'=>'span5'));

	echo $form->textFieldRow($model,'level',array('class'=>'span5'));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64)); ?>

$this->endWidget();

?>
