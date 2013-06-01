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
	protected $defaultSort = array('t.due' => 'DESC', 'description');

	public function tableName() {
		return 'v_duty';
	}
	
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// simulate coming into Duty instead of Dashboard duty
		$duty = new Duty('search');
		if(isset($_GET['DashboardDuty']))
		{
			$duty->attributes = $_GET['DashboardDuty'];
		}

		$criteria = $duty->searchCriteria;

		// filter to current active duties for this user
//		$criteria->compare('derived_assigned_to_id', Yii::app()->user->id);
		$criteria->compareNull('updated');
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
 //       $columns[] = static::linkColumn('searchInCharge', 'User', 'assignedTo');
        $columns[] = 'derived_importance';
		$columns[] = 'due:date';
//		$columns[] = 'updated:datetime';

		return $columns;
	}

	public function assertFromParent($modelName = null)
	{

	}
	
	public function afterFind() {
	}

}

?>