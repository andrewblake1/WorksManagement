<?php

Yii::import('application.models._base.BaseDuty');

class Duty extends BaseDuty
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}