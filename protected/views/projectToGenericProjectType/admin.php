<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchGenericProjectType',
			'value'=>'CHtml::link($data->searchGenericProjectType,
				Yii::app()->createUrl("GenericProjectType/update", array("id"=>$data->generic_project_type_id))
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
         array(
			'name'=>'searchGeneric',
			'value'=>'CHtml::link($data->searchGeneric,
				Yii::app()->createUrl("Generic/update", array("id"=>$data->generic_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
