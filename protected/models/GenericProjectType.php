<?php

Yii::import('application.models._base.BaseGenericProjectType');

class GenericProjectType extends BaseGenericProjectType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}