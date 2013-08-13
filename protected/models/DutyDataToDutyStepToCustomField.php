<?php

/**
 * This is the model class for table "tbl_dutyData_to_duty_step_to_custom_field".
 *
 * The followings are the available columns in table 'tbl_dutyData_to_duty_step_to_custom_field':
 * @property string $id
 * @property integer $duty_step_to_custom_field_id
 * @property string $duty_data_id
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property DutyStepToCustomField $dutyStepToCustomField
 * @property DutyData $dutyData
 */
class DutyDataToDutyStepToCustomField extends CustomValueActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomField;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyStepToCustomField' => array(self::BELONGS_TO, 'DutyStepToCustomField', 'duty_step_to_custom_field_id'),
            'dutyData' => array(self::BELONGS_TO, 'DutyData', 'duty_data_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchCustomField', $this->searchCustomField, 'COALESCE(dutyStepToCustomField.label_override, customField.label)', true);

		// with
		$criteria->with=array(
			'dutyStepToCustomField',
			'dutyStepToCustomField.customField',
		);

		return $criteria;
	}
	
	static function getDisplayAttr()
	{
		return array(
			'searchCustomField',
		);
	}
	
	public function beforeValidate()
	{
		// set any custom validators
		$this->customValidatorParams = array(
			'customField' => $this->dutyStepToCustomField->customField,
			'params' => array('relationToCustomField'=>'dutyStepToCustomField->customField'),
		);

		return parent::beforeValidate();
	}
	
	public function createSave(&$models = array()) {
		$this->setDefault($this->dutyStepToCustomField->customField);
		
		return parent::createSave($models);
	}

	public function getHtmlId($attribute) {
		return CHtml::activeId($this, "[{$this->dutyStepToCustomField->customField->id}]custom_value");
	}
}

?>