<?php

/**
 * This is the model class for table "tbl_custom_field_to_task_template".
 *
 * The followings are the available columns in table 'tbl_custom_field_to_task_template':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $custom_field_id
 * @property integer $custom_field_task_category_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomField $customField
 * @property CustomFieldTaskCategory $customFieldTaskCategory
 * @property User $updatedBy
 * @property TaskTemplate $taskTemplate
 * @property TaskToCustomFieldToTaskTemplate[] $taskToCustomFieldToTaskTemplates
 */
class CustomFieldToTaskTemplate extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTaskTemplate;
	public $searchCustomFieldTaskCategory;
	public $searchCustomField;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_template_id, custom_field_id', 'required'),
			array('task_template_id, custom_field_id', 'numerical', 'integerOnly'=>true),
			array('custom_field_task_category_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_template_id, searchTaskTemplate, searchCustomFieldTaskCategory, searchCustomField', 'safe', 'on'=>'search'),
		);
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
            'customFieldTaskCategory' => array(self::BELONGS_TO, 'CustomFieldTaskCategory', 'custom_field_task_category_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'taskToCustomFieldToTaskTemplates' => array(self::HAS_MANY, 'TaskToCustomFieldToTaskTemplate', 'custom_field_to_task_template_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_template_id' => 'Client/Project type/Task type',
			'searchTaskTemplate' => 'Client/Project type/Task type',
			'custom_field_task_category_id' => 'Task category',
			'searchCustomFieldTaskCategory' => 'Task category',
			'custom_field_id' => 'Custom type',
			'searchCustomField' => 'Custom type',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.custom_field_task_category_id',
			't.custom_field_id',
			'customFieldTaskCategory.name AS searchCustomFieldTaskCategory',
			'customField.description AS searchCustomField',
		);

		// where
		$criteria->compare('customFieldTaskCategory.name',$this->searchCustomFieldTaskCategory,true);
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('t.task_template_id',$this->task_template_id);

		// with
		$criteria->with = array(
			'customFieldTaskCategory',
			'customField',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchCustomFieldTaskCategory', 'CustomFieldTaskCategory', 'custom_field_task_category_id');
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

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchCustomFieldTaskCategory', 'searchCustomField');
	}

}

?>