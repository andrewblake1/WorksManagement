<?php 

$this->widget('adminViewWidget',array(
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
			'name'=>'searchResourceType',
			'value'=>'CHtml::link($data->searchResourceType,
				Yii::app()->createUrl("ResourceType/update", array("id"=>$data->resource_type_id))
			)',
			'type'=>'raw',
		),
		'quantity',
		'hours',
		'start',
	),
));

?>
