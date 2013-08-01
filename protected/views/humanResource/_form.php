<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AuthItemController::listWidgetRow($model, $form, 'auth_item_name');

	ActionController::listWidgetRow($model, $form, 'action_id');

$this->endWidget();

?>