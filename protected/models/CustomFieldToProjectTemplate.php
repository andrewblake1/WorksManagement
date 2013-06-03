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
			array('custom_field_id, custom_field_project_category_id', 'required'),
			array('project_template_id, custom_field_id, custom_field_project_category_id, show_in_admin, show_in_planning', 'numerical', 'integerOnly'=>true),
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
			'project_template_id' => 'Project template',
			'searchProjectTemplate' => 'Project template',
			'custom_field_project_category_id' => 'Project category',
			'searchCustomFieldProjectCategory' => 'Project category',
			'custom_field_id' => 'Custom field',
			'show_in_admin' => 'Show in admin page',
			'show_in_planning' => 'Show in planning page',
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
			't.show_in_admin',
			't.show_in_planning',
		);

		// where
		$criteria->compare('customField.description',$this->searchCustomField,true);
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
		$columns[] = static::linkColumn('searchCustomField', 'CustomField', 'custom_field_id');
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
			'customField->description',
		);
	}

	public function beforeSave() {
		$customFieldProjectCategory = CustomFieldProjectCategory::model()->findByPk($this->custom_field_project_category_id); 
		$this->project_template_id = $customFieldProjectCategory->project_template_id;
		return parent::beforeSave();
	}
/*	// needed because the only id passed from the custom field project category scren is the category id - nence need to get this models parent
	public function assertFromParent()
	{
// TODO: repeated in task, an day
		// if update in planning view
		if(isset($_GET['controller']['Planning']) && isset($_GET['project_id']))
		{
			// ensure that that at least the parents primary key is set for the admin view of planning
			Controller::setAdminParam('project_id', $_GET['project_id'], 'Planning');
		}
		
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(!(Yii::app()->controller->id == 'planning'))
		{
			return parent::assertFromParent();
		}
	}*/

}

?>