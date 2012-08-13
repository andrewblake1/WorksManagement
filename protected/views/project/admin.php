<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
		'travel_time_1_way',
		'critical_completion',
		'planned',
	),
)); ?>
