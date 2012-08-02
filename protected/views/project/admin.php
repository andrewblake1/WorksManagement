<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
		'travel_time_1_way',
		'critical_completion',
		'planned',
         array(
			'name'=>'searchClient',
			'value'=>'CHtml::link($data->searchClient,
				Yii::app()->createUrl("Client/update", array("id"=>$data->client_id))
			)',
			'type'=>'raw',
		),
		'',
	),
)); ?>
