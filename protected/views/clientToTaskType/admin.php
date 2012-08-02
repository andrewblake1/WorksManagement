<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchClient',
			'value'=>'CHtml::link($data->searchClient,
				Yii::app()->createUrl("Client/update", array("id"=>$data->client_id))
			)',
			'type'=>'raw',
		),
        array(
			'name'=>'searchTaskType',
			'value'=>'CHtml::link($data->searchTaskType,
				Yii::app()->createUrl("TaskType/update", array("id"=>$data->task_type_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
