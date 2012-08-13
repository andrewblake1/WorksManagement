<?php 

$this->widget('AdminViewWidget',array(
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
			'name'=>'searchTemplateProject',
			'value'=>'CHtml::link($data->searchTemplateProject,
				Yii::app()->createUrl("TemplateProject/update", array("id"=>$data->template_project_id))
			)',
			'type'=>'raw',
		),
	),
));

?>
