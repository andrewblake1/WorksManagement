<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
         array(
			'name'=>'searchProjectType',
			'value'=>'CHtml::link($data->searchProjectType,
				Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
			)',
			'type'=>'raw',
		),
		'travel_time_1_way',
		'critical_completion',
		'planned',
	),
)); ?>
