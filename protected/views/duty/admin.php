<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchTask',
			'value'=>'CHtml::link($data->searchTask,
				Yii::app()->createUrl("Task/update", array("id"=>$data->task_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchProjectToAuthAssignmentToTaskTypeToDutyType',
			'value'=>'CHtml::link($data->searchProjectToAuthAssignmentToTaskTypeToDutyType,
				Yii::app()->createUrl("ProjectToAuthAssignmentToTaskTypeToDutyType/update", array("id"=>$data->project_to_AuthAssignment_to_task_type_to_duty_type_id))
			)',
			'type'=>'raw',
		),
		'updated',
         array(
			'name'=>'searchGeneric',
			'value'=>'CHtml::link($data->searchGeneric,
				Yii::app()->createUrl("Generic/update", array("id"=>$data->generic_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
