<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('role');

	ContactController::listWidgetRow($model, $form, 'contact_id');

$this->endWidget();

?>