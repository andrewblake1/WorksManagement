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
'description',
array(
			'name' => 'genericTaskCategory',
			'type' => 'raw',
			'value' => $model->genericTaskCategory !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->genericTaskCategory)), array('generictaskcategory/view', 'id' => GxActiveRecord::extractPkValue($model->genericTaskCategory, true))) : null,
			),
array(
			'name' => 'genericType',
			'type' => 'raw',
			'value' => $model->genericType !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->genericType)), array('genericType/view', 'id' => GxActiveRecord::extractPkValue($model->genericType, true))) : null,
			),
'deleted:boolean',
array(
			'name' => 'staff',
			'type' => 'raw',
			'value' => $model->staff !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->staff)), array('staff/view', 'id' => GxActiveRecord::extractPkValue($model->staff, true))) : null,
			),
	),
)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->taskToGenericTaskTypes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('taskToGenericTaskType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes1')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->taskToGenericTaskTypes1 as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('taskToGenericTaskType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes2')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->taskToGenericTaskTypes2 as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('taskToGenericTaskType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?>