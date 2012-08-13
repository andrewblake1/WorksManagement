<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchTaskType',
			'value'=>'CHtml::link($data->searchTaskType,
				Yii::app()->createUrl("TaskType/update", array("id"=>$data->task_type_id))
			)',
			'type'=>'raw',
		),
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
