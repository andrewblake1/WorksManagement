<?php

/**
 * This is the model class for table "tbl_custom_field_to_project_template".
 *
 * The followings are the available columns in table 'tbl_custom_field_to_project_template':
 * @property integer $id
 * @property integer $project_template_id
 * @property integer $custom_field_id
 * @property integer $custom_field_project_category_id
 * @property integer $show_in_admin
 * @property integer $show_in_planning
 * @property string $label_override
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
	public $searchCustomFieldLabel;
	public $searchCustomFieldComment;
	
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
			'project_template_id' => 'Project template',
			'searchProjectTemplate' => 'Project template',
			'custom_field_project_category_id' => 'Custom field set',
			'searchCustomFieldProjectCategory' => 'Custom field set',
			'custom_field_id' => 'Custom field',
			'show_in_admin' => 'Show in admin page',
			'show_in_planning' => 'Show in planning page',
			'searchCustomFieldLabel' => 'Custom field',
			'searchCustomFieldComment' => 'Custom field comment',
            'label_override' => 'Label override',
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
			't.label_override',
			'customField.label AS searchCustomFieldLabel',
			'customField.comment AS searchCustomFieldComment',
			't.show_in_admin',
			't.show_in_planning',
		);

		// where
		$criteria->compare('customField.label',$this->searchCustomFieldLabel,true);
		$criteria->compare('customField.comment',$this->searchCustomFieldComment,true);
		$criteria->compare('t.label_override',$this->label_override,true);
		$criteria->compare('t.custom_field_project_category_id',$this->custom_field_project_category_id);
		$criteria->compare('t.show_in_admin',Yii::app()->format->toMysqlBool($this->show_in_admin));
		$criteria->compare('t.show_in_planning',Yii::app()->format->toMysqlBool($this->show_in_planning));
		
		// with 
		$criteria->with = array(
			'customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = static::linkColumn('searchCustomFieldLabel', 'CustomField', 'custom_field_id');
		$columns[] = 'show_in_admin:boolean';
		$columns[] = 'show_in_planning:boolean';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'label_override',
			'searchCustomFieldLabel',
			'searchCustomFieldComment',
		);
	}

	public function beforeSave() {
		$customFieldProjectCategory = CustomFieldProjectCategory::model()->findByPk($this->custom_field_project_category_id); 
		$this->project_template_id = $customFieldProjectCategory->project_template_id;
		return parent::beforeSave();
	}

}

?>