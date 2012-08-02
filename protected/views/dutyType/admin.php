<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
		'lead_in_days',
         array(
			'name'=>'searchDutyCategory',
			'value'=>'CHtml::link($data->searchDutyCategory,
				Yii::app()->createUrl("DutyCategory/update", array("id"=>$data->duty_category_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchGenericType',
			'value'=>'CHtml::link($data->searchGenericType,
				Yii::app()->createUrl("GenericType/update", array("id"=>$data->generic_type_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
