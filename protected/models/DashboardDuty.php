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

		// need to create the temp table that we will use - required to get the accumlated total - only want to do one shot though hence the atatic
		static $called = false;

		if(!$called && $this->scenario == 'search')
		{
			// create argument string for procedure call that generates the temporary table used here */
			// (IN in_planning_id INT, IN in_action_id INT, IN in_derived_assigned_to_id INT)
			$args = empty($_GET['task_id']) ? 'NULL' : $_GET['task_id'];
			$args .= ", ";
			$args .= empty($_GET['action_id']) ? 'NULL' : $_GET['action_id'];
			$user = User::model()->findByPk(Yii::app()->user->id);
			$args .= ", {$user->contact_id}";

			//NB: need this in here rather than in tableName() so can be called externally
			Yii::app()->db->createCommand("CALL pro_get_duties_from_planning($args)")->execute();
			$called = true;

			return $tableName = 'tmp_duty';
		}

		return ($this->scenario == 'search') || static::$inSearch
			? 'v_duty'
			: 'tbl_duty';
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
		$criteria->compare('t.derived_assigned_to_id', $user->contact_id);
		$criteria->compare('t.wbs', $this->wbs, true);
		$criteria->compare('t.project_name', $this->project_name, true);
		$criteria->compare('t.action_description', $this->action_description, true);
		$criteria->compare('t.due', $this->due);
		$criteria->compareNull('t.updated');
		$criteria->addCondition("t.planned IS NOT NULL");
		
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