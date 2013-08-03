<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	DutyStepToCustomField::dependantListWidgetRow($model, $form, 'duty_step_to_custom_field_id', 'DutyStep', 'duty_step_id', array(), array('scopeDutyStep'=>array($model->duty_step_id)));

$this->endWidget();

?>