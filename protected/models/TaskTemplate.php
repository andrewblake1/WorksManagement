<?php

/**
 * This is the model class for table "tbl_task_template".
 *
 * The followings are the available columns in table 'tbl_task_template':
 * @property integer $id
 * @property string $description
 * @property integer $project_template_id
 * @property integer $client_id
 * @property string $unit_price
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomFieldToTaskTemplate[] $customFieldToTaskTemplates
 * @property Task[] $tasks
 * @property User $updatedBy
 * @property ProjectTemplate $projectTemplate
 * @property ProjectTemplate $client
 * @property TaskTemplateToAssembly[] $taskTemplateToAssemblies
 * @property TaskTemplateToAssemblyGroup[] $taskTemplateToAssemblyGroups
 * @property TaskTemplateToAction[] $taskTemplateToActions
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskTemplateToMaterialGroup[] $taskTemplateToMaterialGroups
 * @property TaskTemplateToResource[] $taskTemplateToResources
 */
class TaskTemplate extends ActiveRecord
{

	public function scopeProjectTemplate($crew_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('crew.id',$crew_id);
		$criteria->join='
			 JOIN tbl_project_template projectTemplate ON t.project_template_id = projectTemplate.id
			 JOIN tbl_project project ON projectTemplate.id = project.project_template_id
			 JOIN tbl_day day ON project.id = day.project_id
			 JOIN tbl_crew crew ON day.id = crew.day_id
		';

		$this->getDbCriteria()->mergeWith($criteria);
	
		return $this;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description, project_template_id, client_id', 'required'),
			array('project_template_id, client_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('unit_price', 'length', 'max'=>7),
			array('quantity_tooltip', 'length', 'max'=>255),
			array('select', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, description, project_template_id, unit_price, quantity, minimum, maximum, select, quantity_tooltip, deleted, updated_by', 'safe', 'on'=>'search'),
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
            'customFieldToTaskTemplates' => array(self::HAS_MANY, 'CustomFieldToTaskTemplate', 'task_template_id'),
            'tasks' => array(self::HAS_MANY, 'Task', 'task_template_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'client' => array(self::BELONGS_TO, 'ProjectTemplate', 'client_id'),
            'taskTemplateToAssemblies' => array(self::HAS_MANY, 'TaskTemplateToAssembly', 'task_template_id'),
            'taskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskTemplateToAssemblyGroup', 'task_template_id'),
            'taskTemplateToActions' => array(self::HAS_MANY, 'TaskTemplateToAction', 'task_template_id'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'task_template_id'),
            'taskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskTemplateToMaterialGroup', 'task_template_id'),
            'taskTemplateToResources' => array(self::HAS_MANY, 'TaskTemplateToResource', 'task_template_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'project_template_id' => 'Project type',
			'unit_price' => 'Unit price',
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
			't.description',
			't.unit_price',
			't.select',
			't.quantity_tooltip',
			't.quantity',
			't.minimum',
			't.maximum',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.unit_price',$this->unit_price);
		$criteria->compare('t.project_template_id',$this->project_template_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.select',$this->select,true);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'unit_price';
  		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
		$columns[] = 'quantity_tooltip';
 		
		return $columns;
	}

	public function beforeValidate()
	{
		$projectTemplate = ProjectTemplate::model()->findByPk($this->project_template_id);
		$this->client_id = $projectTemplate->client_id;
		
		return parent::beforeValidate();
	}

}

?>