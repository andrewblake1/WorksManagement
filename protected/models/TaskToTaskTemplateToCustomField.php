<?php

/**
 * This is the model class for table "tbl_task_to_task_template_to_custom_field".
 *
 * The followings are the available columns in table 'tbl_task_to_task_template_to_custom_field':
 * @property string $id
 * @property string $task_id
 * @property integer $task_template_to_custom_field_id
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property TaskTemplateToCustomField $taskTemplateToCustomField
 */
class TaskToTaskTemplateToCustomField extends CustomValueActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomField;
	
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
            'taskTemplateToCustomField' => array(self::BELONGS_TO, 'TaskTemplateToCustomField', 'task_template_to_custom_field_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchCustomField', $this->searchCustomField, 'COALESCE(taskTemplateToCustomField.label_override, customField.label)', true);

		$criteria->with=array(
			'taskTemplateToCustomField.customField',
		);

		return $criteria;
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
			'customField' => $this->taskTemplateToCustomField->customField,
			'params' => array('relationToCustomField'=>'taskTemplateToCustomField->customField'),
		);

		return parent::beforeValidate();
	}

	public function createSave(&$models = array()) {
		$this->setDefault($this->taskTemplateToCustomField->customField);

		return parent::createSave($models);
	}

	public function getHtmlId($attribute) {
		return CHtml::activeId($this, "[{$this->taskTemplateToCustomField->customField->id}]custom_value");
	}
}

?>