<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ClientContactController::listWidgetRow($model, $form, 'client_contact_id');

$this->endWidget();

?>