<?php

Yii::import('application.models._base.BaseDay');

class Day extends BaseDay
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}