<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$this->widget('WMEJuiAutoCompleteTableColumn', array('model'=>$model, 'form'=>$form));

	$form->textAreaRow('select');

$this->endWidget();

?>