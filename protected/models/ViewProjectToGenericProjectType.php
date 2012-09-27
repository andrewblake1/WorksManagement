<?php

class ViewProjectToGenericProjectType extends ViewGenericActiveRecord
{
	public $project_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_project_to_generic_project_type';
	}

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