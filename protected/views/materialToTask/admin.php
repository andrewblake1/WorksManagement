<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchMaterial',
			'value'=>'CHtml::link($data->searchMaterial,
				Yii::app()->createUrl("Material/update", array("id"=>$data->material_id))
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
		'quantity',
	),
));

?>
