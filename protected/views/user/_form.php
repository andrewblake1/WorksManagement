<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ContactController::listWidgetRow($model, $form, 'contact_id');

	$form->passwordFieldRow('password');

$this->endWidget();

?>
