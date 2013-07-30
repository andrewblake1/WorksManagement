<?php

/**
 * This is the model class for table "tbl_duty_step_branch".
 *
 * The followings are the available columns in table 'tbl_duty_step_branch':
 * @property integer $id
 * @property string $duty_step_dependency_id
 * @property integer $duty_step_to_custom_field_id
 * @property string $compare
 * @property integer $duty_step_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepToCustomField $dutyStepToCustomField
 * @property User $updatedBy
 * @property DutyStepDependency $dutyStepDependency
 * @property DutyStepToCustomField $dutyStep
 */
class DutyStepBranch extends ActiveRecord
{
	public $searchCustomField;
	
	static $niceNamePlural = 'Conditions';
	static $niceName = 'Condition';
	
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
			'dutyStepDependency' => array(self::BELONGS_TO, 'DutyStepDependency', 'duty_step_dependency_id'),
			'dutyStep' => array(self::BELONGS_TO, 'DutyStepToCustomField', 'duty_step_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'duty_step_to_custom_field_id' => 'Custom field',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'COALESCE(dutyStepToCustomField.label_override, customField.label) AS searchCustomField',
			't.compare',
		);

		// where
		$criteria->compare('t.duty_step_dependency_id',$this->duty_step_dependency_id);
		$criteria->compare('t.compare',$this->compare, true);
		$criteria->compare('COALESCE(dutyStepToCustomField.label_override, customField.label',$this->searchCustomField, true);
		
		// with
		$criteria->with = array(
			'dutyStepToCustomField',
			'dutyStepToCustomField.customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchCustomField';
		$columns[] = 'compare';
		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchCustomField',
			'compare',
		);
	}
 
}

?>