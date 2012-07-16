<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' ' . $model->label(2), 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
'id',
'description',
array(
			'name' => 'day0',
			'type' => 'raw',
			'value' => $model->day0 !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->day0)), array('day/view', 'id' => GxActiveRecord::extractPkValue($model->day0, true))) : null,
			),
array(
			'name' => 'purchaseOrders',
			'type' => 'raw',
			'value' => $model->purchaseOrders !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->purchaseOrders)), array('purchaseOrders/view', 'id' => GxActiveRecord::extractPkValue($model->purchaseOrders, true))) : null,
			),
array(
			'name' => 'crew',
			'type' => 'raw',
			'value' => $model->crew !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->crew)), array('crew/view', 'id' => GxActiveRecord::extractPkValue($model->crew, true))) : null,
			),
array(
			'name' => 'project',
			'type' => 'raw',
			'value' => $model->project !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->project)), array('project/view', 'id' => GxActiveRecord::extractPkValue($model->project, true))) : null,
			),
array(
			'name' => 'clientToTaskTypeClient',
			'type' => 'raw',
			'value' => $model->clientToTaskTypeClient !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->clientToTaskTypeClient)), array('clientToTaskType/view', 'id' => GxActiveRecord::extractPkValue($model->clientToTaskTypeClient, true))) : null,
			),
array(
			'name' => 'clientToTaskTypeTaskType',
			'type' => 'raw',
			'value' => $model->clientToTaskTypeTaskType !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->clientToTaskTypeTaskType)), array('clientToTaskType/view', 'id' => GxActiveRecord::extractPkValue($model->clientToTaskTypeTaskType, true))) : null,
			),
array(
			'name' => 'staff',
			'type' => 'raw',
			'value' => $model->staff !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->staff)), array('staff/view', 'id' => GxActiveRecord::extractPkValue($model->staff, true))) : null,
			),
	),
)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('clientToTaskTypeToDutyTypes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->clientToTaskTypeToDutyTypes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('clientToTaskTypeToDutyType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('materials')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->materials as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('material/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('reschedules')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->reschedules as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('reschedule/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('reschedules1')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->reschedules1 as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('reschedule/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('assemblies')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->assemblies as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('assembly/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->taskToGenericTaskTypes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('taskToGenericTaskType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('resourceTypes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->resourceTypes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('resourceType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('taskTypes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->taskTypes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('taskType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?>