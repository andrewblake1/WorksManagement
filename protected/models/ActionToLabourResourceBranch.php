<?php

/**
 * This is the model class for table "tbl_action_to_labour_resource_branch".
 *
 * The followings are the available columns in table 'tbl_action_to_labour_resource_branch':
 * @property integer $id
 * @property integer $duty_step_to_custom_field_id
 * @property integer $action_to_labour_resource_id
 * @property string $action_id
 * @property string $compare
 * @property integer $duty_step_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepToCustomField $dutyStepToCustomField
 * @property User $updatedBy
 * @property DutyStepToCustomField $dutyStep
 * @property ActionToLabourResource $action
 * @property ActionToLabourResource $actionToLabourResource
 */
class ActionToLabourResourceBranch extends ActiveRecord
{
	static $niceNamePlural = 'Conditions';
	static $niceName = 'Condition';
	
	public $searchDutyStep;
	public $searchCustomField;

	/*
	 * these just here for purpose of tabs - ensuring these variables exist ensures than can be added to the url from currrent $_GET
	 */
	public $client_id;
	public $project_template_id;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'dutyStepToCustomField' => array(self::BELONGS_TO, 'DutyStepToCustomField', 'duty_step_to_custom_field_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
			'action' => array(self::BELONGS_TO, 'ActionToLabourResource', 'action_id'),
			'actionToLabourResource' => array(self::BELONGS_TO, 'ActionToLabourResource', 'action_to_labour_resource_id'),
		);
	}

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchDutyStep', $this->searchDutyStep, 'dutyStep.description', true);
		$criteria->compareAs('searchCustomField', $this->searchCustomField, 'COALESCE(dutyStepToCustomField.label_override, customField.label)', true);

		$criteria->with=array(
			'dutyStep',
			'dutyStepToCustomField',
			'dutyStepToCustomField.customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchDutyStep';
        $columns[] = 'searchCustomField';
        $columns[] = 'compare';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchDutyStep',
			'searchCustomField',
		);
	}
	
	public function beforeValidate()
	{
		if($this->duty_step_to_custom_field_id)
		{
			$this->action_id = $this->dutyStepToCustomField->dutyStep->action_id;
		}

		return parent::beforeValidate();
	}
 }
