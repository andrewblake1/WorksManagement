<?php

Yii::import('application.models._base.BasePlan');

class Plan extends BasePlan
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}