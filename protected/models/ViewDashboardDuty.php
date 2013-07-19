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
	
	public static function createTmpDuty()
	{
		// create argument string for procedure call that generates the temporary table used here */
		// (IN in_planning_id INT, IN in_action_id INT, IN in_derived_assigned_to_id INT)
		$args = empty($_GET['task_id']) ? 'NULL' : $_GET['task_id'];
		$args .= ", ";
		$args .= empty($_GET['action_id']) ? 'NULL' : $_GET['action_id'];
		$user = User::model()->findByPk(Yii::app()->user->id);
		$args .= ", {$user->contact_id}";

		Yii::app()->db->createCommand("CALL pro_get_duties_from_planning($args)")->execute();
	}

	public function getAdminColumns()
	{
		return DashboardDuty::getAdminColumns();
	}

}

?>