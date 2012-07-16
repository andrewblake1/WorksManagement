<?php

Yii::import('application.models._base.BaseGenericTaskType');

class GenericTaskType extends BaseGenericTaskType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}