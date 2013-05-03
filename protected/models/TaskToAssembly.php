<?php

/**
 * This is the model class for table "task_to_assembly".
 *
 * The followings are the available columns in table 'task_to_assembly':
 * @property string $id
 * @property string $task_id
 * @property integer $assembly_id
 * @property integer $sub_assembly_id
 * @property string $parent_id
 * @property integer $quantity
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Staff $staff
 * @property SubAssembly $subAssembly
 * @property Assembly $assembly
 * @property TaskToAssembly $parent
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups1
 * @property TaskToAssemblyToTaskTypeToAssemblyGroup[] $taskToAssemblyToTaskTypeToAssemblyGroups
 * @property TaskToAssemblyToTaskTypeToAssemblyGroup[] $taskToAssemblyToTaskTypeToAssemblyGroups1
 * @property TaskToMaterial[] $taskToMaterials
 */
class TaskToAssembly extends AdjacencyListActiveRecord
{
	public $store_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->customValidators + array(
			array('task_id, quantity, assembly_id', 'required'),
			array('assembly_id, sub_assembly_id, quantity', 'numerical', 'integerOnly'=>true),
			array('parent_id, task_id', 'length', 'max'=>10),
		);
	}

	public function setCustomValidators()
	{
		// if sub assembly
		if($this->parent_id)
		{
			$this->setCustomValidatorsRange($model->subAssembly);
		}
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
            'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
            'subAssembly' => array(self::BELONGS_TO, 'SubAssembly', 'sub_assembly_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'parent' => array(self::BELONGS_TO, 'TaskToAssembly', 'parent_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'parent_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'task_to_assembly_id'),
            'taskToAssemblyToTaskTypeToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTypeToAssemblyGroup', 'assembly_id'),
            'taskToAssemblyToTaskTypeToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTypeToAssemblyGroup', 'task_to_assembly_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'task_to_assembly_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'assembly_id' => 'Assembly',
			'searchTask' => 'Task',
			'searchAssemblyDescription' => 'Assembly',
			'searchAssemblyAlias' => 'Aliases',
			'searchTaskQuantity' => 'Task quantity',
			'searchTotalQuantity' => 'Total',
			'searchAssemblyGroup' => 'Group/Comment',
		));
	}

	static function getDisplayAttr()
	{
		return array(
			'assembly->description',
			'assembly->alias',
		);
	}

	public function afterFind() {
		$this->store_id = $this->assembly->store_id;
		
		return parent::afterFind();
	}
	
	/*
	 * to be overidden if using mulitple models
	 */
	public function createSave(&$models=array())
	{
		return TaskToAssemblyController::addAssembly($this->task_id, $this->assembly_id, $this->quantity, $this->parent_id, $this->sub_assembly_id, $models, $this);
	}
	
}

?>