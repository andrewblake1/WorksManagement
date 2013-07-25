<?php

/**
 * This is the model class for table "tbl_duty_step_branch".
 *
 * The followings are the available columns in table 'tbl_duty_step_branch':
 * @property integer $id
 * @property string $duty_step_dependency_id
 * @property integer $custom_field_to_duty_step_id
 * @property string $compare
 * @property integer $duty_step_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomFieldToDutyStep $customFieldToDutyStep
 * @property User $updatedBy
 * @property DutyStepDependency $dutyStepDependency
 * @property CustomFieldToDutyStep $dutyStep
 */
class DutyStepBranch extends ActiveRecord
{
	public $searchCustomField;
	
	static $niceNamePlural = 'Conditions';
	static $niceName = 'Condition';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('duty_step_dependency_id, custom_field_to_duty_step_id, compare, duty_step_id, updated_by', 'required'),
			array('custom_field_to_duty_step_id, duty_step_id, updated_by', 'numerical', 'integerOnly'=>true),
			array('duty_step_dependency_id', 'length', 'max'=>10),
			array('compare', 'length', 'max'=>255),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'customFieldToDutyStep' => array(self::BELONGS_TO, 'CustomFieldToDutyStep', 'custom_field_to_duty_step_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'dutyStepDependency' => array(self::BELONGS_TO, 'DutyStepDependency', 'duty_step_dependency_id'),
			'dutyStep' => array(self::BELONGS_TO, 'CustomFieldToDutyStep', 'duty_step_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'custom_field_to_duty_step_id' => 'Custom field',
			'searchCustomField' => 'Custom field',
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
			'COALESCE(customFieldToDutyStep.label_override, customField.label) AS searchCustomField',
			't.compare',
		);

		// where
		$criteria->compare('t.duty_step_dependency_id',$this->duty_step_dependency_id);
		$criteria->compare('t.compare',$this->compare, true);
		$criteria->compare('COALESCE(customFieldToDutyStep.label_override, customField.label',$this->searchCustomField, true);
		
		// with
		$criteria->with = array(
			'customFieldToDutyStep',
			'customFieldToDutyStep.customField',
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