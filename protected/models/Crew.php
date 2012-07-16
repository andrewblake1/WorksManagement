<?php

Yii::import('application.models._base.BaseCrew');

class Crew extends BaseCrew
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}