<?php

/**
 * This is the Nested Set  model class for table "genericprojectcategory".
 *
 * The followings are the available columns in table 'genericprojectcategory':
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
 * @property GenericprojectType[] $genericprojectTypes
 * @property Staff $staff
 */
class Genericprojectcategory extends CategoryActiveRecord {

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Project category';

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'genericprojectTypes' => array(self::HAS_MANY, 'GenericprojectType', 'genericprojectcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'Project category',
		) + parent::attributeLabels();
	}

}