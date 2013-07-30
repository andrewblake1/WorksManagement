<?php

/**
 * This is the model class for table "tbl_custom_field_to_duty_step".
 *
 * The followings are the available columns in table 'tbl_custom_field_to_duty_step':
 * @property integer $id
 * @property integer $custom_field_duty_step_category_id
 * @property integer $duty_step_id
 * @property integer $custom_field_id
 * @property string $label_override
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property CustomFieldDutyStepCategory $dutyStep
 * @property CustomField $customField
 * @property CustomFieldDutyStepCategory $customFieldDutyStepCategory
 * @property DutyDataToCustomFieldToDutyStep[] $dutyDataToCustomFieldToDutySteps
 * @property DutyStepBranch[] $dutyStepBranches
 * @property DutyStepBranch[] $dutyStepBranches1
 */
class CustomFieldToDutyStep extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomFieldLabel;
	public $searchCustomFieldComment;

	public function scopeDutyStep($duty_step_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('duty_step_id', $duty_step_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
            'customFieldDutyStepCategory' => array(self::BELONGS_TO, 'CustomFieldDutyStepCategory', 'custom_field_duty_step_category_id'),
            'customFieldProjectCategory' => array(self::BELONGS_TO, 'CustomFieldProjectCategory', 'custom_field_project_category_id'),
            'dutyDataToCustomFieldToDutySteps' => array(self::HAS_MANY, 'DutyDataToCustomFieldToDutyStep', 'custom_field_to_duty_step_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'custom_field_duty_step_category_id' => 'Custom field set',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.custom_field_duty_step_category_id',
			't.custom_field_id',
			't.label_override',
			'customField.label AS searchCustomFieldLabel',
			'customField.comment AS searchCustomFieldComment',
		);

		// where
		$criteria->compare('customField.label',$this->searchCustomFieldLabel,true);
		$criteria->compare('customField.comment',$this->searchCustomFieldComment,true);
		$criteria->compare('t.label_override',$this->label_override,true);
		$criteria->compare('t.custom_field_duty_step_category_id',$this->custom_field_duty_step_category_id);

		// with 
		$criteria->with = array(
			'customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		return array(
			'label_override',
			'searchCustomFieldLabel',
			'searchCustomFieldComment',
		);
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchCustomFieldLabel',
			'label_override',
		);
	}

	public function beforeSave() {
		$customFieldDutyStepCategory = CustomFieldDutyStepCategory::model()->findByPk($this->custom_field_duty_step_category_id); 
		$this->duty_step_id = $customFieldDutyStepCategory->duty_step_id;
		return parent::beforeSave();
	}

}

?>