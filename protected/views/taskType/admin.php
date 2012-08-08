<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
         array(
			'name'=>'searchClient',
			'value'=>'CHtml::link($data->searchClient,
				Yii::app()->createUrl("Client/update", array("id"=>$data->client_id))
			)',
			'type'=>'raw',
		),
         array(
			'name'=>'searchTemplateTask',
			'value'=>'CHtml::link($data->searchTemplateTask,
				Yii::app()->createUrl("TemplateTask/update", array("id"=>$data->template_task_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
