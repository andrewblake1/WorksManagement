<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ContactController::listWidgetRow($model, $form, 'contact_id');

$this->endWidget();

?>