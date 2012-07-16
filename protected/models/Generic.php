<?php

Yii::import('application.models._base.BaseGeneric');

class Generic extends BaseGeneric
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}