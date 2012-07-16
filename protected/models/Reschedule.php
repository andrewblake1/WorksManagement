<?php

Yii::import('application.models._base.BaseReschedule');

class Reschedule extends BaseReschedule
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}