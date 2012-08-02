<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	SupplierController::listWidgetRow($model, $form, 'supplier_id');

	echo $form->textFieldRow($model,'number',array('class'=>'span5','maxlength'=>64));

$this->endWidget();

?>
