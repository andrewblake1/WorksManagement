<?php

/**
 * This is the model class for table "tbl_dutyData_to_custom_field_to_duty_step".
 *
 * The followings are the available columns in table 'tbl_dutyData_to_custom_field_to_duty_step':
 * @property string $id
 * @property integer $custom_field_to_duty_step_id
 * @property string $duty_data_id
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property CustomFieldToDutyStep $customFieldToDutyStep
 * @property DutyData $dutyData
 */
class DutyDataToCustomFieldToDutyStep extends CustomValueActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomFieldToDutyStep;
	public $searchDutyData;
	public $searchCustomField;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('custom_field_to_duty_step_id, duty_data_id', 'required'),
			array('custom_field_to_duty_step_id', 'numerical', 'integerOnly'=>true),
			array('duty_data_id', 'length', 'max'=>10),
			array('custom_value', 'length', 'max'=>255),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'customFieldToDutyStep' => array(self::BELONGS_TO, 'CustomFieldToDutyStep', 'custom_field_to_duty_step_id'),
            'dutyData' => array(self::BELONGS_TO, 'DutyData', 'duty_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'custom_field_to_duty_step_id' => 'Custom field',
			'searchCustomFieldToDutyStep' => 'Custom field',
			'duty_data_id' => 'Client/DutyData',
			'searchDutyData' => 'Client/DutyData',
			'custom_value' => 'Custom Value',
			'searchCustomField' => 'Custom Value',
		));
	}

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'customField.description AS searchCustomField',
			't.custom_value',
			't.duty_data_id',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.duty_data_id',$this->duty_data_id);
		$criteria->compare('customField.description',$this->searchCustomField, true);
		$criteria->compare('t.custom_value',$this->custom_value, true);

		// with
		$criteria->with=array(
			'customFieldToDutyStep.customField',
		);

		return $criteria;
	}
	
	static function getDisplayAttr()
	{
		return array('customFieldToDutyStep->customField->description');
	}
	
	public function beforeValidate()
	{
		// set any custom validators
		$this->customValidatorParams = array(
			'customField' => $this->customFieldToDutyStep->customField,
			'params' => array('relationToCustomField'=>'dutyDataToCustomFieldToDutyStep->customFieldToDutyStep->customField'),
		);

		return parent::beforeValidate();
	}
	
	public function createSave(&$models = array()) {
		$this->setDefault($this->customFieldToDutyStep->customField);
		
		return parent::createSave($models);
	}

}

?>