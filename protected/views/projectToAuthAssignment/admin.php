<h1>Manage Project To Auth Assignments</h1>

<?php $this->widget('bootstrap.widgets.BootAlert'); ?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'project-to-auth-assignment-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'project_id',
		'AuthAssignment_id',
		'staff_id',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>
