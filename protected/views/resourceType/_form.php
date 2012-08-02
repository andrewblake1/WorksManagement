<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	resourceCategoryController::listWidgetRow($model, $form, 'resource_category_id');

	echo $form->textFieldRow($model,'maximum',array('class'=>'span5'));

$this->endWidget();

?>
