<?php

/**
 * This is the model class for table "tbl_custom_field_task_category".
 *
 * The followings are the available columns in table 'tbl_custom_field_task_category':
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
 * @property CustomFieldToTaskTemplate[] $customFieldToTaskTemplates
 */
class CustomFieldTaskCategory extends CategoryActiveRecord {
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Task category';

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
		return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'customFieldToTaskTemplates' => array(self::HAS_MANY, 'CustomFieldToTaskTemplate', 'custom_field_category_id'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'Task category',
		) + parent::attributeLabels();
	}

}