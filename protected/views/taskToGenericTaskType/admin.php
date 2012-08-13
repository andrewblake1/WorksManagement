<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchGenericTaskType',
			'value'=>'CHtml::link($data->searchGenericTaskType,
				Yii::app()->createUrl("GenericTaskType/update", array("id"=>$data->generic_task_type_id))
			)',
			'type'=>'raw',
		),
        array(
			'name'=>'searchTask',
			'value'=>'CHtml::link($data->searchTask,
				Yii::app()->createUrl("Task/update", array("id"=>$data->task_id))
			)',
			'type'=>'raw',
		),
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
