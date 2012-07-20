<h1>Manage Duties</h1>

<?php $this->widget('bootstrap.widgets.BootAlert'); ?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'duty-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'task_id',
		'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id',
		'updated',
		'generic_id',
		'staff_id',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>
