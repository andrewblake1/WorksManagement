<?php

/**
 * This is the model class for table "tbl_duty_step_to_custom_field".
 *
 * The followings are the available columns in table 'tbl_duty_step_to_custom_field':
 * @property integer $id
 * @property integer $custom_field_duty_step_category_id
 * @property integer $duty_step_id
 * @property integer $custom_field_id
 * @property string $label_override
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ActionToLabourResourceBranch[] $actionToLabourResourceBranches
 * @property ActionToLabourResourceBranch[] $actionToLabourResourceBranches1
 * @property ActionToPlantBranch[] $actionToPlantBranches
 * @property ActionToPlantBranch[] $actionToPlantBranches1
 * @property DutyDataToDutyStepToCustomField[] $dutyDataToDutyStepToCustomFields
 * @property DutyStepBranch[] $dutyStepBranches
 * @property CustomField $customField
 * @property CustomFieldDutyStepCategory $customFieldDutyStepCategory
 * @property DutyStep $dutyStep
 * @property User $updatedBy
 */
class DutyStepToCustomField extends ActiveRecord
{

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomFieldLabel;
	public $searchCustomFieldComment;

	// place holders used by getValidGetParam in order to retain correct breadcrumb tail and these needed
	// due to the multi levels available within actions - a dirty hack. Otherwise calculation of breadcrumb trail
	// will loose the fact that have come thru action - may
	public $project_template_id;
	public $client_id;
	public $action_id;

	public function rules($ignores = array())
	{
		return parent::rules(array('duty_step_id'));
	}

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
            'actionToLabourResourceBranches' => array(self::HAS_MANY, 'ActionToLabourResourceBranch', 'duty_step_to_custom_field_id'),
            'actionToLabourResourceBranches1' => array(self::HAS_MANY, 'ActionToLabourResourceBranch', 'duty_step_id'),
            'actionToPlantBranches' => array(self::HAS_MANY, 'ActionToPlantBranch', 'duty_step_to_custom_field_id'),
            'actionToPlantBranches1' => array(self::HAS_MANY, 'ActionToPlantBranch', 'duty_step_id'),
            'dutyDataToDutyStepToCustomFields' => array(self::HAS_MANY, 'DutyDataToDutyStepToCustomField', 'duty_step_to_custom_field_id'),
            'dutyStepBranches' => array(self::HAS_MANY, 'DutyStepBranch', 'duty_step_to_custom_field_id'),
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'customFieldDutyStepCategory' => array(self::BELONGS_TO, 'CustomFieldDutyStepCategory', 'custom_field_duty_step_category_id'),
            'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
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
		$criteria=new DbCriteria($this);

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->compareAs('searchCustomFieldLabel', $this->searchCustomFieldLabel, 'customField.label', true);
		$criteria->compareAs('searchCustomFieldComment', $this->searchCustomFieldComment, 'customField.comment', true);

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
			'label_override',
			'searchCustomFieldLabel',
		);
	}

	public function beforeSave() {
		$customFieldDutyStepCategory = CustomFieldDutyStepCategory::model()->findByPk($this->custom_field_duty_step_category_id); 
		$this->duty_step_id = $customFieldDutyStepCategory->duty_step_id;
		return parent::beforeSave();
	}

}

?>