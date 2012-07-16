<?php

Yii::import('application.models._base.BaseTask');

class Task extends BaseTask
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}