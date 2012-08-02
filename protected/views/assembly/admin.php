<?php

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
        array(
			'name'=>'url',
			'value'=>'CHtml::link($data->url, $data->url)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchMaterial',
			'value'=>'CHtml::link($data->searchMaterial,
				Yii::app()->createUrl("Material/update", array("id"=>$data->material_id))
			)',
			'type'=>'raw',
		),
 		'quantity',
	),
));

?>
