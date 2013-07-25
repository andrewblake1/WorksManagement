<?php

/**
 * This is the model class for table "tbl_resource_category".
 *
 * The followings are the available columns in table 'tbl_resource_category':
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
 * @property Resource[] $resources
 * @property User $updatedBy
 */
class ResourceCategory extends CategoryActiveRecord {

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource group';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE1: you should only define rules for those attributes that
		// will receive user inputs.
		// NOTE2: Remove ALL rules associated with the nested Behavior:
		//rgt,lft,root,level,id.
		return array_merge(parent::rules(), array(
			array('name', 'required'),
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
            'resources' => array(self::HAS_MANY, 'Resource', 'resource_category_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'Resource category',
		) + parent::attributeLabels();
	}

}