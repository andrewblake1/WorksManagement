<h1>Manage Assemblies</h1>

<?php $this->widget('bootstrap.widgets.BootAlert'); ?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'assembly-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'plan_id',
		'material_id',
		'quantity',
		'deleted',
		'staff_id',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>
