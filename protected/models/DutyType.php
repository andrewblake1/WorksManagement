<?php

/**
 * This is the model class for table "tbl_duty_type".
 *
 * The followings are the available columns in table 'tbl_duty_type':
 * @property string $id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepDependency[] $dutyStepDependencies
 * @property User $updatedBy
 * @property TaskTemplateToDutyType[] $taskTemplateToDutyTypes
 */
class DutyType extends ActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description, updated_by', 'required'),
			array('deleted, updated_by', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
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
			'dutyStepDependencies' => array(self::HAS_MANY, 'DutyStepDependency', 'duty_type_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'taskTemplateToDutyTypes' => array(self::HAS_MANY, 'TaskTemplateToDutyType', 'tbl_duty_type_id'),
		);
	}

}