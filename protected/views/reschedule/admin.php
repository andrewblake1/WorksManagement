<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchOldTask',
			'value'=>'CHtml::link($data->searchOldTask,
				Yii::app()->createUrl("Task/update", array("id"=>$data->old_task_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchNewTask',
			'value'=>'CHtml::link($data->searchNewTask,
				Yii::app()->createUrl("Task/update", array("id"=>$data->new_task_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
