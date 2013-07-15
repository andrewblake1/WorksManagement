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
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DutyStepBranch the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_duty_step_branch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, duty_step_dependency_id, custom_field_to_duty_step_id, compare, duty_step_id, updated_by', 'required'),
			array('id, custom_field_to_duty_step_id, duty_step_id, updated_by', 'numerical', 'integerOnly'=>true),
			array('duty_step_dependency_id', 'length', 'max'=>10),
			array('compare', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, duty_step_dependency_id, custom_field_to_duty_step_id, compare, duty_step_id, updated_by', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'duty_step_dependency_id' => 'Duty Step Dependency',
			'custom_field_to_duty_step_id' => 'Custom Field To Duty Step',
			'compare' => 'Compare',
			'duty_step_id' => 'Duty Step',
			'updated_by' => 'Updated By',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('duty_step_dependency_id',$this->duty_step_dependency_id,true);
		$criteria->compare('custom_field_to_duty_step_id',$this->custom_field_to_duty_step_id);
		$criteria->compare('compare',$this->compare,true);
		$criteria->compare('duty_step_id',$this->duty_step_id);
		$criteria->compare('updated_by',$this->updated_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}