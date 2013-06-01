<?php

/**
 * This is the model class for table "tbl_duty".
 *
 * The followings are the available columns in table 'tbl_duty':
 * @property string $id
 * @property string $task_id
 * @property string $duty_data_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property DutyData $dutyData
 */
class DashboardDuty extends Duty
{
	public function tableName() {
		return Duty::model()->tableName();
	}
	
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria= Duty::model()->searchCriteria;

		// filter to current active duties for this user
//		$criteria->compare('assignedTo', 1);
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
 //       $columns[] = static::linkColumn('searchInCharge', 'User', 'assignedTo');
        $columns[] = 'searchImportance';
		$columns[] = 'due:date';
		$columns[] = 'updated:datetime';

		return $columns;
	}

	public function assertFromParent($modelName = null)
	{

	}

}

?>