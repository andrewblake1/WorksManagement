<?php

/**
 * This is the model class for table "tbl_project_to_project_template_to_custom_field".
 *
 * The followings are the available columns in table 'tbl_project_to_project_template_to_custom_field':
 * @property string $id
 * @property integer $project_template_to_custom_field_id
 * @property string $project_id
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property ProjectTemplateToCustomField $projectTemplateToCustomField
 * @property Project $project
 */
class ProjectToProjectTemplateToCustomField extends CustomValueActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProject;
	public $searchCustomField;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectTemplateToCustomField' => array(self::BELONGS_TO, 'ProjectTemplateToCustomField', 'project_template_to_custom_field_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchCustomField', $this->searchCustomField, 'COALESCE(projectTemplateToCustomField.label_override, customField.label)', true);

		$criteria->with=array(
			'projectTemplateToCustomField',
			'projectTemplateToCustomField.customField',
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
			'customField' => $this->projectTemplateToCustomField->customField,
			'params' => array('relationToCustomField'=>'projectToProjectTemplateToCustomField->projectTemplateToCustomField->customField'),
		);

		return parent::beforeValidate();
	}
	
	public function createSave(&$models = array()) {
		$this->setDefault($this->projectTemplateToCustomField->customField);
		
		return parent::createSave($models);
	}

	public function getHtmlId($attribute) {
		return CHtml::activeId($this, "[{$this->projectTemplateToCustomField->customField->id}]custom_value");
	}
}

?>