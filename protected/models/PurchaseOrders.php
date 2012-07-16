<?php

Yii::import('application.models._base.BasePurchaseOrders');

class PurchaseOrders extends BasePurchaseOrders
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}