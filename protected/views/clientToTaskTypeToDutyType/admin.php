<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchDutyType',
			'value'=>'CHtml::link($data->searchDutyType,
				Yii::app()->createUrl("DutyType/update", array("id"=>$data->duty_type_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchClientToTaskType',
			'value'=>'CHtml::link($data->searchClientToTaskType,
				Yii::app()->createUrl("ClientToTaskType/update", array("id"=>$data->client_to_task_type_id))
			)',
			'type'=>'raw',
		),
        array(
			'name'=>'AuthItem_name',
			'value'=>'CHtml::link($data->AuthItem_name,
				Yii::app()->createUrl("AuthItem/update", array("id"=>$data->AuthItem_name))
			)',
			'type'=>'raw',
		),
	),
));

?>
