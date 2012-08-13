<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'preferred_date',
		'earliest_date',
		'date_scheduled',
		array(
			'name'=>'searchInCharge',
			'value'=>'CHtml::link($data->searchInCharge,
				Yii::app()->createUrl("Staff/update", array("id"=>$data->in_charge_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
