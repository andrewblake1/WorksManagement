<?php

Yii::import('application.models._base.BaseSupplier');

class Supplier extends BaseSupplier
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}