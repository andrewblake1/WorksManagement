<?php

Yii::import('application.models._base.BaseClientToTaskType');

class ClientToTaskType extends BaseClientToTaskType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}