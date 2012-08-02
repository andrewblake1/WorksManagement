<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'itemname',
         array(
			'name'=>'searchUser',
			'value'=>'CHtml::link($data->searchUser,
				Yii::app()->createUrl("Staff/update", array("id"=>$data->userid))
			)',
			'type'=>'raw',
		),
		'bizrule',
		'data',
	),
));

?>
