<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
         array(
			'name'=>'searchResourceCategory',
			'value'=>'CHtml::link($data->searchResourceCategory,
				Yii::app()->createUrl("ResourceCategory/update", array("id"=>$data->resource_category_id))
			)',
			'type'=>'raw',
		),
		'maximum',
	),
));

?>
