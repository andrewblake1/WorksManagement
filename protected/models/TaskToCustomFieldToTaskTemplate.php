<?php

/**
 * This is the model class for table "tbl_task_to_custom_field_to_task_template".
 *
 * The followings are the available columns in table 'tbl_task_to_custom_field_to_task_template':
 * @property string $id
 * @property string $task_id
 * @property integer $custom_field_to_task_template_id
 * @property string $custom_value_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property CustomValue $customValue
 * @property User $updatedBy
 * @property CustomFieldToTaskTemplate $customFieldToTaskTemplate
 */
class TaskToCustomFieldToTaskTemplate extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomFieldToTaskTemplate;
	public $searchTask;
	public $searchCustomField;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom type';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('custom_field_to_task_template_id, task_id', 'required'),
			array('custom_field_to_task_template_id', 'numerical', 'integerOnly'=>true),
			array('task_id, custom_value_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, task_id, searchCustomFieldToTaskTemplate, searchTask, searchCustomField', 'safe', 'on'=>'search'),
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
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
            'customValue' => array(self::BELONGS_TO, 'CustomValue', 'custom_value_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'customFieldToTaskTemplate' => array(self::BELONGS_TO, 'CustomFieldToTaskTemplate', 'custom_field_to_task_template_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'custom_field_to_task_template_id' => 'Task type/Custom type)',
			'searchCustomFieldToTaskTemplate' => 'Task type/Custom type)',
			'task_id' => 'Client/Task',
			'searchTask' => 'Client/Task',
			'custom_value_id' => 'Custom value',
			'searchCustomField' => 'Custom value',
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
			't.custom_field_to_task_template_id',
			"CONCAT_WS('$delimiter',
				taskTemplate.description,
				customField.description
				) AS searchCustomFieldToTaskTemplate",
		);

		// where
		$this->compositeCriteria($criteria, array(
			'taskTemplate.description',
			'customField.description',
			), $this->searchCustomFieldToTaskTemplate);
		$criteria->compare('t.task_id',$this->task_id);

		// with
		$criteria->with = array(
			'customFieldToTaskTemplate.taskTemplate',
			'customFieldToTaskTemplate.customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchCustomFieldToTaskTemplate', 'CustomFieldToTaskTemplate', 'custom_field_to_task_template_id');
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array('customFieldToTaskTemplate->customField->description');
	}
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchCustomFieldToTaskTemplate', 'searchTask', 'searchCustomField');
	}
	
	/*
	 * overidden as mulitple models i.e. nothing to save in this model as this model can either be deleted or created as the data item resides in customValue
	 */
	public function updateSave(&$models = array())
	{
		$customValue = $this->customValue;

		// massive assignement
		$customValue->attributes=$_POST['CustomValue'][$customValue->id];

		// validate and save NB: only saving the customValue here as nothing else should change
		return $customValue->updateSave($models, array(
			'customField' => $this->customFieldToTaskTemplate->customField,
			'params' => array('relationToCustomField'=>'taskToCustomFieldToTaskTemplate->customFieldToTaskTemplate->customField'),
		));
	}

	/*
	 * overidden as mulitple models i.e. nothing to save in this model as this model can either be deleted or created as the data item resides in customValue
	 */
	public function createSave(&$models = array())
	{
		$saved = CustomValue::createCustomField($this->customFieldToTaskTemplate, $models, $customValue);
		$this->custom_value_id = $customValue->id;

		return $saved & parent::createSave($models);
	}

}

?>