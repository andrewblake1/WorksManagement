<h1>Manage Project To Auth Assignment To Client To Task Type To Duty Types</h1>

<?php $this->widget('bootstrap.widgets.BootAlert'); ?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'project-to-auth-assignment-to-client-to-task-type-to-duty-type-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'project_to_AuthAssignment_id',
		'client_to_task_type_to_duty_type_id',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>
