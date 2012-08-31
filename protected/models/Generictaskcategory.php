<?php

/**
 * This is the Nested Set  model class for table "generictaskcategory".
 *
 * The followings are the available columns in table 'generictaskcategory':
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
 * @property GenerictaskType[] $generictaskTypes
 * @property Staff $staff
 */
class Generictaskcategory extends CategoryActiveRecord {
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Task category';

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'generictaskcategory';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'generictaskTypes' => array(self::HAS_MANY, 'GenerictaskType', 'generictaskcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

}