<?php

Yii::import('application.models._base.BaseStaff');

class Staff extends BaseStaff
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}