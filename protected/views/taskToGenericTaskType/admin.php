<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'task_id',
         array(
			'name'=>'searchGenericTaskType',
			'value'=>'CHtml::link($data->searchGenericTaskType,
				Yii::app()->createUrl("GenericTaskType/update", array("id"=>$data->generic_task_type_id))
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
