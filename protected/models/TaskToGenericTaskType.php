<?php

Yii::import('application.models._base.BaseTaskToGenericTaskType');

class TaskToGenericTaskType extends BaseTaskToGenericTaskType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}