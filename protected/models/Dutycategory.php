<?php

/**
 * This is the Nested Set  model class for table "dutycategory".
 *
 * The followings are the available columns in table 'dutycategory':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyType[] $dutyTypes
 * @property Staff $staff
 */
class Dutycategory extends CategoryActiveRecord {

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty category';


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
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'dutyTypes' => array(self::HAS_MANY, 'DutyType', 'dutycategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

}