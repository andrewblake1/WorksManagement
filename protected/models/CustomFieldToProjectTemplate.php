<?php

/**
 * This is the model class for table "tbl_custom_field_to_project_template".
 *
 * The followings are the available columns in table 'tbl_custom_field_to_project_template':
 * @property integer $id
 * @property integer $project_template_id
 * @property integer $custom_field_id
 * @property integer $custom_field_project_category_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomField $customField
 * @property User $updatedBy
 * @property CustomFieldProjectCategory $projectTemplate
 * @property CustomFieldProjectCategory $customFieldProjectCategory
 * @property ProjectToCustomFieldToProjectTemplate[] $projectToCustomFieldToProjectTemplates
 */
class CustomFieldToProjectTemplate extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectTemplate;
	public $searchCustomFieldProjectCategory;
	public $searchCustomField;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('project_template_id, custom_field_id, custom_field_project_category_id', 'required'),
			array('project_template_id, custom_field_id, custom_field_project_category_id,', 'numerical', 'integerOnly'=>true),
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
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectTemplate' => array(self::BELONGS_TO, 'CustomFieldProjectCategory', 'project_template_id'),
            'customFieldProjectCategory' => array(self::BELONGS_TO, 'CustomFieldProjectCategory', 'custom_field_project_category_id'),
            'projectToCustomFieldToProjectTemplates' => array(self::HAS_MANY, 'ProjectToCustomFieldToProjectTemplate', 'custom_field_to_project_template_id'),
        );
    }



	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'project_template_id' => 'Client/Project type',
			'searchProjectTemplate' => 'Client/Project type',
			'custom_field_project_category_id' => 'Project category',
			'searchCustomFieldProjectCategory' => 'Project category',
			'custom_field_id' => 'Custom field',
			'searchCustomField' => 'Custom field',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.custom_field_project_category_id',
			't.custom_field_id',
			'customField.description AS searchCustomField',
		);

		// where
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('t.custom_field_project_category_id',$this->custom_field_project_category_id);
		
		// with 
		$criteria->with = array(
			'customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = static::linkColumn('searchCustomField', 'CustomField', 'custom_field_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'customField->description',
		);
	}

}

?>