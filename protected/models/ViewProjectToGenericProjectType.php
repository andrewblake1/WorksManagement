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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('project_id', 'safe', 'on'=>'search')
		));
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