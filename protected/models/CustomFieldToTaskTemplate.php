<?php

/**
 * This is the model class for table "tbl_custom_field_to_task_template".
 *
 * The followings are the available columns in table 'tbl_custom_field_to_task_template':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $custom_field_id
 * @property integer $custom_field_task_category_id
 * @property integer $show_in_admin
 * @property integer $show_in_planning
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomField $customField
 * @property User $updatedBy
 * @property CustomFieldTaskCategory $taskTemplate
 * @property CustomFieldTaskCategory $customFieldTaskCategory
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
		return array_merge(parent::rules(), array(
			array('custom_field_id, custom_field_task_category_id', 'required'),
			array('task_template_id, custom_field_id, custom_field_task_category_id, show_in_admin, show_in_planning', 'numerical', 'integerOnly'=>true),
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
            'taskTemplate' => array(self::BELONGS_TO, 'CustomFieldTaskCategory', 'task_template_id'),
            'customFieldTaskCategory' => array(self::BELONGS_TO, 'CustomFieldTaskCategory', 'custom_field_task_category_id'),
            'taskToCustomFieldToTaskTemplates' => array(self::HAS_MANY, 'TaskToCustomFieldToTaskTemplate', 'custom_field_to_task_template_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_template_id' => 'Task template',
			'searchTaskTemplate' => 'Task templat',
			'custom_field_task_category_id' => 'Custom field set',
			'searchCustomFieldTaskCategory' => 'Custom field set',
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
			't.custom_field_task_category_id',
			't.custom_field_id',
			'customField.description AS searchCustomField',
			't.show_in_admin',
			't.show_in_planning',
		);

		// where
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('t.custom_field_task_category_id',$this->custom_field_task_category_id);
		$criteria->compare('t.custom_field_task_category_id',$this->custom_field_task_category_id);
		
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
		$customFieldTaskCategory = CustomFieldTaskCategory::model()->findByPk($this->custom_field_task_category_id); 
		$this->task_template_id = $customFieldTaskCategory->task_template_id;
		return parent::beforeSave();
	}

}

?>