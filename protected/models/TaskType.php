<?php

/**
 * This is the model class for table "task_type".
 *
 * The followings are the available columns in table 'task_type':
 * @property integer $id
 * @property string $description
 * @property integer $project_type_id
 * @property integer $client_id
 * @property string $unit_price
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericTaskType[] $genericTaskTypes
 * @property Task[] $tasks
 * @property Staff $staff
 * @property ProjectType $projectType
 * @property ProjectType $client
 * @property TaskTypeToAssembly[] $taskTypeToAssemblies
 * @property TaskTypeToAssemblyGroup[] $taskTypeToAssemblyGroups
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
 * @property TaskTypeToMaterial[] $taskTypeToMaterials
 * @property TaskTypeToMaterialGroup[] $taskTypeToMaterialGroups
 * @property TaskTypeToResourceType[] $taskTypeToResourceTypes
 */
class TaskType extends ActiveRecord
{

	public function scopeProjectType($crew_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('crew.id',$crew_id);
		$criteria->join='
			 JOIN project_type on t.project_type_id = project_type.id
			 JOIN project on project_type.id = project.project_type_id
			 JOIN day on project.id = day.project_id
			 JOIN crew on day.id = crew.day_id
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
		return array(
			array('description, project_type_id, client_id', 'required'),
			array('project_type_id, client_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('unit_price', 'length', 'max'=>7),
			array('quantity_tooltip', 'length', 'max'=>255),
			array('select', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, project_type_id, unit_price, quantity, minimum, maximum, select, quantity_tooltip, deleted, staff_id', 'safe', 'on'=>'search'),
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
            'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'task_type_id'),
            'tasks' => array(self::HAS_MANY, 'Task', 'task_type_id'),
            'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
            'client' => array(self::BELONGS_TO, 'ProjectType', 'client_id'),
            'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
            'taskTypeToAssemblies' => array(self::HAS_MANY, 'TaskTypeToAssembly', 'task_type_id'),
            'taskTypeToAssemblyGroups' => array(self::HAS_MANY, 'TaskTypeToAssemblyGroup', 'task_type_id'),
            'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'task_type_id'),
            'taskTypeToMaterials' => array(self::HAS_MANY, 'TaskTypeToMaterial', 'task_type_id'),
            'taskTypeToMaterialGroups' => array(self::HAS_MANY, 'TaskTypeToMaterialGroup', 'task_type_id'),
            'taskTypeToResourceTypes' => array(self::HAS_MANY, 'TaskTypeToResourceType', 'task_type_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'project_type_id' => 'Project type',
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
		$criteria->compare('t.project_type_id',$this->project_type_id);
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
		$projectType = ProjectType::model()->findByPk($this->project_type_id);
		$this->client_id = $projectType->client_id;
		
		return parent::beforeValidate();
	}

}

?>