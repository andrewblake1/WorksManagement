<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchClientToTaskType',
			'value'=>'CHtml::link($data->searchClientToTaskType,
				Yii::app()->createUrl("ClientToTaskType/update", array("id"=>$data->client_to_task_type_id))
			)',
			'type'=>'raw',
		),
		'description',
         array(
			'name'=>'searchGenericTaskCategory',
			'value'=>'CHtml::link($data->searchGenericTaskCategory,
				Yii::app()->createUrl("GenericTaskCategory/update", array("id"=>$data->generic_task_category_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchGenericType',
			'value'=>'CHtml::link($data->searchGenericType,
				Yii::app()->createUrl("GenericType/update", array("id"=>$data->generic_type_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
