<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	Yii::t('app', 'Manage'),
);

$this->menu = array(
		array('label'=>Yii::t('app', 'List') . ' ' . $model->label(2), 'url'=>array('index')),
		array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('task-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t('app', 'Manage') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo GxHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'task-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'id',
		'description',
		array(
				'name'=>'day',
				'value'=>'GxHtml::valueEx($data->day0)',
				'filter'=>GxHtml::listDataEx(Day::model()->findAllAttributes(null, true)),
				),
		array(
				'name'=>'purchase_orders_id',
				'value'=>'GxHtml::valueEx($data->purchaseOrders)',
				'filter'=>GxHtml::listDataEx(PurchaseOrders::model()->findAllAttributes(null, true)),
				),
		array(
				'name'=>'crew_id',
				'value'=>'GxHtml::valueEx($data->crew)',
				'filter'=>GxHtml::listDataEx(Crew::model()->findAllAttributes(null, true)),
				),
		array(
				'name'=>'project_id',
				'value'=>'GxHtml::valueEx($data->project)',
				'filter'=>GxHtml::listDataEx(Project::model()->findAllAttributes(null, true)),
				),
		/*
		array(
				'name'=>'client_to_task_type_client_id',
				'value'=>'GxHtml::valueEx($data->clientToTaskTypeClient)',
				'filter'=>GxHtml::listDataEx(ClientToTaskType::model()->findAllAttributes(null, true)),
				),
		array(
				'name'=>'client_to_task_type_task_type_id',
				'value'=>'GxHtml::valueEx($data->clientToTaskTypeTaskType)',
				'filter'=>GxHtml::listDataEx(ClientToTaskType::model()->findAllAttributes(null, true)),
				),
		array(
				'name'=>'staff_id',
				'value'=>'GxHtml::valueEx($data->staff)',
				'filter'=>GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true)),
				),
		*/
		array(
			'class' => 'CButtonColumn',
		),
	),
)); ?>