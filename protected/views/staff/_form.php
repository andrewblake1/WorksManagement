<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'first_name',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'last_name',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'phone_mobile',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>255));

	echo $form->passwordFieldRow($model,'password',array('class'=>'span5','maxlength'=>32));

$this->endWidget();

?>
