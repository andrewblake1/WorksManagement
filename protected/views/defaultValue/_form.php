<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$this->widget('WMEJuiAutoCompleteTableColumn', array('model'=>$model, 'form'=>$form));

	$form->textAreaRow('select');

$this->endWidget();

?>