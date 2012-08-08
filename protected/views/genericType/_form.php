<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	echo $form->checkBoxRow($model,'mandatory');	

	echo $form->checkBoxRow($model,'allow_new');

	echo $form->dropDownListRow(
		$model,
		'validation_type', $model->validationTypes);
	
	echo $form->dropDownListRow(
		$model,
		'data_type', $model->dataTypes);

	echo $form->textAreaRow($model,'validation_text',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textAreaRow($model,'validation_error',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

$this->endWidget();

?>
