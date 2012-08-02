<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchSupplier',
			'value'=>'CHtml::link($data->searchSupplier,
				Yii::app()->createUrl("Supplier/update", array("id"=>$data->supplier_id))
			)',
			'type'=>'raw',
		),
		'number',
	),
));

?>
