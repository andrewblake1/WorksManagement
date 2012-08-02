<?php 

$this->widget('adminViewWidget',array(
	'model'=>$model,
	'columns'=>array(
		'id',
		'first_name',
		'last_name',
        array(
			'name'=>'phone_mobile',
			'value'=>'CHtml::link($data->phone_mobile, "tel:".$data->phone_mobile)',
			'type'=>'raw',
		),
        array(
			'name'=>'email',
			'value'=>'$data->email',
			'type'=>'email',
		),
  	),
));

?>
