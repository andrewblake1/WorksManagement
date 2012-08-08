<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchProjectType',
			'value'=>'CHtml::link($data->searchProjectType,
				Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchGenericProjectCategory',
			'value'=>'CHtml::link($data->searchGenericProjectCategory,
				Yii::app()->createUrl("GenericProjectCategory/update", array("id"=>$data->generic_project_category_id))
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
