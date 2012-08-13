<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'root',
		'lft',
		'rgt',
		'level',
         array(
			'name'=>'searchDutyCategory',
			'value'=>'CHtml::link($data->searchDutyCategory,
				Yii::app()->createUrl("DutyCategory/update", array("id"=>$data->duty_category_id))
			)',
			'type'=>'raw',
		),
		'description',
	),
));

?>
