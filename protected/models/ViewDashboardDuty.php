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
		return 'v_duty';
	}

	public function getAdminColumns()
	{
		return DashboardDuty::getAdminColumns();
	}

}

?>