<?php

/**
 * This is the Nested Set  model class for table "resourcecategory".
 *
 * The followings are the available columns in table 'resourcecategory':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $dutycategory_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ResourceType[] $resourceTypes
 * @property Dutycategory $dutycategory
 * @property Staff $staff
 */
class Resourcecategory extends CategoryActiveRecord {

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource category';


	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'resourcecategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE1: you should only define rules for those attributes that
		// will receive user inputs.
		// NOTE2: Remove ALL rules associated with the nested Behavior:
		//rgt,lft,root,level,id.
		return array(
			array('name, staff_id', 'required'),
			array('dutycategory_id', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 64),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'resourceTypes' => array(self::HAS_MANY, 'ResourceType', 'resourcecategory_id'),
			'dutycategory' => array(self::BELONGS_TO, 'Dutycategory', 'dutycategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'Resource category',
//			'dutycategory_id' => 'Dutycategory',
		) + parent::attributeLabels();
	}

}