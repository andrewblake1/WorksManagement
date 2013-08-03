<?php

/**
 * This is the model class for table "tbl_action_to_human_resource_branch".
 *
 * The followings are the available columns in table 'tbl_action_to_human_resource_branch':
 * @property integer $id
 * @property integer $duty_step_to_custom_field_id
 * @property integer $action_to_human_resource_id
 * @property string $action_id
 * @property string $compare
 * @property integer $duty_step_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepToCustomField $dutyStepToCustomField
 * @property User $updatedBy
 * @property DutyStepToCustomField $dutyStep
 * @property ActionToHumanResource $action
 * @property ActionToHumanResource $actionToHumanResource
 */
class ActionToHumanResourceBranch extends ActiveRecord
{
	public $searchDutyStep;

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
			'dutyStep' => array(self::BELONGS_TO, 'DutyStepToCustomField', 'duty_step_id'),
			'action' => array(self::BELONGS_TO, 'ActionToHumanResource', 'action_id'),
			'actionToHumanResource' => array(self::BELONGS_TO, 'ActionToHumanResource', 'action_to_human_resource_id'),
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
 
}
