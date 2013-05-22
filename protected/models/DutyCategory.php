<?php

/**
 * This is the model class for table "tbl_duty_category".
 *
 * The followings are the available columns in table 'tbl_duty_category':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property DutyStep[] $dutySteps
 * @property ResourceCategory[] $resourceCategories
 */
class DutyCategory extends CategoryActiveRecord {

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
 			'id' => 'Duty category',
		) + parent::attributeLabels();
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
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'duty_category_id'),
            'resourceCategories' => array(self::HAS_MANY, 'ResourceCategory', 'duty_category_id'),
        );
    }

}