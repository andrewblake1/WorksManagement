<?php

class ViewTaskToGenericTaskType extends ViewGenericActiveRecord
{
	protected $task_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_task_to_generic_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return parent::rules() + array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('task_id', 'safe', 'on'=>'search'),
		);
	}

	public function getSearchCriteria()
	{
		if(!empty($this->task_id))
		{
			$this->parent_id = $this->task_id;
		}
		
		return parent::getSearchCriteria();
	}
	
}

?>