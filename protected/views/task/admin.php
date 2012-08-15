<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
		'day',
         array(
			'name'=>'searchPurchaseOrder',
			'value'=>'CHtml::link($data->searchPurchaseOrder,
				Yii::app()->createUrl("PurchaseOrder/update", array("id"=>$data->purchase_order_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchCrew',
			'value'=>'CHtml::link($data->searchCrew,
				Yii::app()->createUrl("Crew/update", array("id"=>$data->crew_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchProject',
			'value'=>'CHtml::link($data->searchProject,
				Yii::app()->createUrl("Project/update", array("id"=>$data->project_id))
			)',
			'type'=>'raw',
		),
	),
));
?>
