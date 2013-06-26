<?php

/**
 * This is the model class for table "tbl_project_to_custom_field_to_project_template".
 *
 * The followings are the available columns in table 'tbl_project_to_custom_field_to_project_template':
 * @property string $id
 * @property integer $custom_field_to_project_template_id
 * @property string $project_id
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property CustomFieldToProjectTemplate $customFieldToProjectTemplate
 * @property Project $project
 */
class ProjectToCustomFieldToProjectTemplate extends CustomValueActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomFieldToProjectTemplate;
	public $searchProject;
	public $searchCustomField;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('custom_field_to_project_template_id, project_id', 'required'),
			array('custom_field_to_project_template_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			array('custom_value', 'length', 'max'=>255),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'customFieldToProjectTemplate' => array(self::BELONGS_TO, 'CustomFieldToProjectTemplate', 'custom_field_to_project_template_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'custom_field_to_project_template_id' => 'Custom field',
			'searchCustomFieldToProjectTemplate' => 'Custom field',
			'project_id' => 'Client/Project',
			'searchProject' => 'Client/Project',
			'custom_value' => 'Custom Value',
			'searchCustomField' => 'Custom Value',
		));
	}

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'customField.description AS searchCustomField',
			't.custom_value',
			't.project_id',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.project_id',$this->project_id);
		$criteria->compare('customField.description',$this->searchCustomField, true);
		$criteria->compare('t.custom_value',$this->custom_value, true);

		// with
		$criteria->with=array(
			'customFieldToProjectTemplate.customField',
		);

		return $criteria;
	}
	
	static function getDisplayAttr()
	{
		return array('customFieldToProjectTemplate->customField->description');
	}
	
	public function beforeValidate()
	{
		// set any custom validators
		$this->customValidatorParams = array(
			'customField' => $this->customFieldToProjectTemplate->customField,
			'params' => array('relationToCustomField'=>'projectToCustomFieldToProjectTemplate->customFieldToProjectTemplate->customField'),
		);

		return parent::beforeValidate();
	}
	
	public function createSave(&$models = array()) {
		$this->setDefault($this->customFieldToProjectTemplate->customField);
		
		return parent::createSave($models);
	}

}

?>