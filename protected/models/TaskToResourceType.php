<?php

Yii::import('application.models._base.BaseTaskToResourceType');

class TaskToResourceType extends BaseTaskToResourceType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}