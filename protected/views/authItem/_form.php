<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>64));

	echo $form->textFieldRow($model,'type',array('class'=>'span5'));

	echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textAreaRow($model,'bizrule',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textAreaRow($model,'data',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

$this->endWidget();

?>
