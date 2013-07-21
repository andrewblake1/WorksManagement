<?php

class ViewDuty extends ViewActiveRecord
{
	protected $defaultSort = array(
		't.updated',
		't.due',
		't.lead_in_days'=>'DESC',
	);

	/**
	 * @var string search variables
	 */
	public $task_id;		// place holder for parent
	public $id;
	public $duty_data_id;
	public $duty_step_id;
	public $description;
	public $due;
	public $derived_assigned_to_id;
	public $derived_assigned_to_name;
	public $derived_updated;
	public $derived_importance;
	public $lead_in_days;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return Duty::rules();
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		return Duty::getSearchCriteria();
	}

	public function getAdminColumns()
	{
		return Duty::getAdminColumns();
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return Duty::getDisplayAttr();
	}

	static function getDisplayAttr()
	{
		return Duty::getDisplayAttr();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return Duty::model()->attributeLabels();
	}
	
	public function tableName()
	{
/*		static $tableName = NULL;

		if(!$tableName)
		{
			// if not updating
			if(empty($_GET['id']) && empty($this->id))
			{
				return $tablename = static::createTmpDuty();
			}
		}*/

		return /*$tableName = */parent::tableName();
	}
	
/*	protected static function getTmpDutyArgs()
	{
		// create argument string for procedure call that generates the temporary table used here 
		// (IN in_planning_id INT, IN in_action_id INT, IN in_derived_assigned_to_id INT)
		$args = empty($_GET['task_id']) ? 'NULL' : $_GET['task_id'];
		$args .= ", ";
		$args .= empty($_GET['action_id']) ? 'NULL' : $_GET['action_id'];
		$args .= ", NULL";

		return $args;
	}
	
	public static function createTmpDuty()
	{
		$args = static::getTmpDutyArgs();
		
		if($args != 'NULL, NULL, NULL')
		{
			//NB: need this in here rather than in tableName() so can be called externally
			Yii::app()->db->createCommand("CALL pro_get_duties_from_planning($args)")->execute();

			return 'tmp_duty';
		}
		else
		{
			return 'v_duty';
		}
	}*/

}

?>