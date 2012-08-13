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
			'name'=>'searchAssembly',
			'value'=>'CHtml::link($data->searchAssembly,
				Yii::app()->createUrl("Assembly/update", array("id"=>$data->assembly_id))
			)',
			'type'=>'raw',
		),
		'quantity',
	),
));

?>
