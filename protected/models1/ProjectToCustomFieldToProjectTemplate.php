<?php

/**
 * This is the model class for table "tbl_project_to_custom_field_to_project_template".
 *
 * The followings are the available columns in table 'tbl_project_to_custom_field_to_project_template':
 * @property string $id
 * @property integer $custom_field_to_project_template_id
 * @property string $project_id
 * @property string $custom_value_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomValue $customValue
 * @property User $updatedBy
 * @property CustomFieldToProjectTemplate $customFieldToProjectTemplate
 * @property Project $project
 */
class ProjectToCustomFieldToProjectTemplate extends ActiveRecord
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
	static $niceName = 'Custom type';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('custom_field_to_project_template_id, project_id', 'required'),
			array('custom_field_to_project_template_id', 'numerical', 'integerOnly'=>true),
			array('project_id, custom_value_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_id, searchCustomFieldToProjectTemplate, searchProject, searchCustomField', 'safe', 'on'=>'search'),
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
            'customValue' => array(self::BELONGS_TO, 'CustomValue', 'custom_value_id'),
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
			'custom_field_to_project_template_id' => 'Custom type',
			'searchCustomFieldToProjectTemplate' => 'Custom type',
			'project_id' => 'Client/Project',
			'searchProject' => 'Client/Project',
			'custom_value_id' => 'Custom Value',
			'searchCustomField' => 'Custom Value',
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
			't.custom_field_to_project_template_id',
			't.custom_value_id',
			'customValue.id AS searchCustomField',
			'customField.description AS searchCustomFieldToProjectTemplate',
		);

		// where
		$criteria->compare('customValue.id',$this->searchCustomField);
		$criteria->compare('customField.description',$this->searchCustomFieldToProjectTemplate);
		$criteria->compare('t.project_id',$this->project_id);

		// with
		$criteria->with = array(
			'customFieldToProjectTemplate.customField',
			'project',
			'customValue',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchCustomFieldToProjectTemplate', 'CustomFieldToProjectTemplate', 'custom_field_to_project_template_id');
        $columns[] = static::linkColumn('searchCustomField', 'CustomValue', 'custom_value_id');
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array('customFieldToProjectTemplate->customField->description');
	}
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchCustomFieldToProjectTemplate', 'searchCustomField');
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
			'customField' => $this->customFieldToProjectTemplate->customField,
			'params' => array('relationToCustomField'=>'projectToCustomFieldToProjectTemplate->customFieldToProjectTemplate->customField'),
		));
	}

	/*
	 * overidden as mulitple models i.e. nothing to save in this model as this model can either be deleted or created as the data item resides in customValue
	 */
	public function createSave(&$models = array())
	{
		$saved = CustomValue::createCustomField($this->customFieldToProjectTemplate, $models, $customValue);
		$this->custom_value_id = $customValue->id;

		return $saved & parent::createSave($models);
	}

}

?>