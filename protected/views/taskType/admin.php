<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'description',
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
