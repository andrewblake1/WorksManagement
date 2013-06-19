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
	public $updated_by;
	protected $defaultSort = array('t.due' => 'ASC', 'description');

	public function tableName() {
		return 'tbl_duty';
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
		$user = User::model()->findByPk(Yii::app()->user->id);
		$criteria->compare('derived_assigned_to_id', $user->contact_id);
		$criteria->compare('wbs', $this->wbs, true);
		$criteria->compare('project_name', $this->project_name, true);
		$criteria->compare('action_description', $this->action_description, true);
		$criteria->compare('due', $this->due);
		$criteria->compareNull('updated');
		$criteria->addCondition("planned IS NOT NULL");
		
		// group by
		$criteria->group = 't.duty_data_id';

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
		$columns[] = 'project_name';
		$columns[] = 'wbs';
		$columns[] = 'action_description';
		$columns[] = 'due:date';

		return $columns;
	}

	public function assertFromParent($modelName = null)
	{

	}
	
	public function afterFind() {
	}

}

?>