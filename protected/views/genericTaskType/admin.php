<h1>Manage Generic Task Types</h1>

<?php $this->widget('bootstrap.widgets.BootAlert'); ?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'generic-task-type-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'client_to_task_type_client_id',
		'client_to_task_type_task_type_id',
		'description',
		'generic_task_category_id',
		'generic_type_id',
		/*
		'deleted',
		'staff_id',
		*/
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>
