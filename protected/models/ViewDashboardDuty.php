<?php

/* traits would be hander here so could inherit from duty and the master view model */

class ViewDashboardDuty extends ViewDuty
{
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		return DashboardDuty::getSearchCriteria();
	}
	
	public function tableName() {

		static $tableName = NULL;
		if(!$tableName)
		{
			$user = User::model()->findByPk(Yii::app()->user->id);
			Yii::app()->db->createCommand("CALL pro_get_duties_from_user({$user->contact_id})")->execute();
			return $tableName = 'tmp_duty';
		}

		return 'v_duty';
	}

	public function getAdminColumns()
	{
		return DashboardDuty::getAdminColumns();
	}

}

?>