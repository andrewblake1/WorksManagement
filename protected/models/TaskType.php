<?php

Yii::import('application.models._base.BaseTaskType');

class TaskType extends BaseTaskType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}