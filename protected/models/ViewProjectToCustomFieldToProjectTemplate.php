<?php

class ViewProjectToCustomFieldToProjectTemplate extends ViewCustomFieldActiveRecord
{
	public $project_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = parent::rules();
		$rules[] = array('project_id', 'safe', 'on'=>'search');
		
		return $rules;
	}

	public function getSearchCriteria()
	{
		if(!empty($this->project_id))
		{
			$this->parent_id = $this->project_id;
		}
		
		return parent::getSearchCriteria();
	}
	
}

?>