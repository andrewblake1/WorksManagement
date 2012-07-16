<?php

Yii::import('application.models._base.BaseGenericType');

class GenericType extends BaseGenericType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}