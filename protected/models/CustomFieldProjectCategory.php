<?php

/**
 * This is the model class for table "tbl_custom_field_project_category".
 *
 * The followings are the available columns in table 'tbl_custom_field_project_category':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $project_template_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property ProjectTemplate $projectTemplate
 * @property CustomFieldToProjectTemplate[] $customFieldToProjectTemplates
 * @property CustomFieldToProjectTemplate[] $customFieldToProjectTemplates1
 */
class CustomFieldProjectCategory extends CategoryActiveRecord {

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field group';
				
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
            array('project_template_id', 'required'),
            array('project_template_id', 'numerical', 'integerOnly'=>true),
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
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'customFieldToProjectTemplates' => array(self::HAS_MANY, 'CustomFieldToProjectTemplate', 'project_template_id'),
            'customFieldToProjectTemplates1' => array(self::HAS_MANY, 'CustomFieldToProjectTemplate', 'custom_field_project_category_id'),
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