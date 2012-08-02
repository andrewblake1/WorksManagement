<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'mandatory',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'allow_new',array('class'=>'span5'));

	echo $form->textFieldRow($model,'validation_type',array('class'=>'span5','maxlength'=>10));

	echo $form->textAreaRow($model,'data_type',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textAreaRow($model,'validation_text',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textAreaRow($model,'validation_error',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

$this->endWidget();

?>
