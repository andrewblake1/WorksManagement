<h1>Manage Project To Generic Project Types</h1>

<?php $this->widget('bootstrap.widgets.BootAlert'); ?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'project-to-generic-project-type-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'generic_project_type_id',
		'project_id',
		'generic_id',
		'staff_id',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>
