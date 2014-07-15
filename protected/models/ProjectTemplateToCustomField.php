<?php

/**
 * This is the model class for table "tbl_project_template_to_custom_field".
 *
 * The followings are the available columns in table 'tbl_project_template_to_custom_field':
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
 * @property ProjectToProjectTemplateToCustomField[] $projectToProjectTemplateToCustomFields
 */
class ProjectTemplateToCustomField extends ActiveRecord
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

	public function rules($ignores = array())
	{
		return parent::rules(array('project_template_id'));
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
            'projectToProjectTemplateToCustomFields' => array(self::HAS_MANY, 'ProjectToProjectTemplateToCustomField', 'project_template_to_custom_field_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'custom_field_project_category_id' => 'Custom field set',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchCustomFieldLabel',$this->searchCustomFieldLabel,'customField.label',true);
		$criteria->compareAs('searchCustomFieldComment', $this->searchCustomFieldComment,'customField.comment',true);
		
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