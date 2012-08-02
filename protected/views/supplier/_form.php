<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>64));

$this->endWidget();

?>
