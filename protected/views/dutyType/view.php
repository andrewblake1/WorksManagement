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
'lead_in_days',
array(
			'name' => 'dutyCategory',
			'type' => 'raw',
			'value' => $model->dutyCategory !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->dutyCategory)), array('dutycategory/view', 'id' => GxActiveRecord::extractPkValue($model->dutyCategory, true))) : null,
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

<h2><?php echo GxHtml::encode($model->getRelationLabel('clientToTaskTypeToDutyTypes')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	foreach($model->clientToTaskTypeToDutyTypes as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('clientToTaskTypeToDutyType/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?>