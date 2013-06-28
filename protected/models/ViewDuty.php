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
}

?>