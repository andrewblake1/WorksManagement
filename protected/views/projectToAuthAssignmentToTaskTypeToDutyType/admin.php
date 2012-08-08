<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchProjectToAuthAssignment',
			'value'=>'CHtml::link($data->searchProjectToAuthAssignment,
				Yii::app()->createUrl("ProjectToAuthAssignment/update", array("id"=>$data->project_to_AuthAssignment_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchTaskTypeToDutyType',
			'value'=>'CHtml::link($data->searchTaskTypeToDutyType,
				Yii::app()->createUrl("TaskTypeToDutyType/update", array("id"=>$data->task_type_to_duty_type_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
