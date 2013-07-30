<?php

/**
 * This is the model class for table "tbl_task_to_custom_field_to_task_template".
 *
 * The followings are the available columns in table 'tbl_task_to_custom_field_to_task_template':
 * @property string $id
 * @property string $task_id
 * @property integer $custom_field_to_task_template_id
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property CustomFieldToTaskTemplate $customFieldToTaskTemplate
 */
class TaskToCustomFieldToTaskTemplate extends CustomValueActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomField;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'customFieldToTaskTemplate' => array(self::BELONGS_TO, 'CustomFieldToTaskTemplate', 'custom_field_to_task_template_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'COALESCE(customFieldToTaskTemplate.label_override, customField.label) AS searchCustomField',
			't.custom_value',
			't.task_id',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.task_id',$this->task_id);
		$criteria->compare('COALESCE(customFieldToTaskTemplate.label_override, customField.label)', $this->searchCustomField, true);
		$criteria->compare('t.custom_value',$this->custom_value, true);

		// with
		$criteria->with=array(
			'customFieldToTaskTemplate.customField',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchCustomFieldToTaskTemplate', 'searchTask', 'searchCustomField');
	}
	
	static function getDisplayAttr()
	{
		return array(
			'searchCustomField',
		);
	}
	
	public function beforeValidate()
	{
		// set any custom validators
		$this->customValidatorParams = array(
			'customField' => $this->customFieldToTaskTemplate->customField,
			'params' => array('relationToCustomField'=>'taskToCustomFieldToTaskTemplate->customFieldToTaskTemplate->customField'),
		);

		return parent::beforeValidate();
	}

	public function createSave(&$models = array()) {
		$this->setDefault($this->customFieldToTaskTemplate->customField);

		return parent::createSave($models);
	}

}

?>