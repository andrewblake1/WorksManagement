<?php

class ViewTaskToCustomFieldToTaskTemplate extends ViewCustomFieldActiveRecord
{
	public $task_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = parent::rules();
		$rules[] = array('task_id', 'safe', 'on'=>'search');
		
		return $rules;
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