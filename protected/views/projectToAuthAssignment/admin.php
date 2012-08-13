<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
         array(
			'name'=>'searchProject',
			'value'=>'CHtml::link($data->searchProject,
				Yii::app()->createUrl("Project/update", array("id"=>$data->project_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchAuthAssignment',
			'value'=>'CHtml::link($data->searchAuthAssignment,
				Yii::app()->createUrl("AuthAssignment/update", array("id"=>$data->AuthAssignment_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
