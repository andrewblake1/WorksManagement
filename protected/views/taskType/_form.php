<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	 echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	TaskController::listWidgetRow($model, $form, 'template_task_id');

$this->endWidget();

?>
