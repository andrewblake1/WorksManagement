<?php

/**
 * This is the model class for table "tbl_task_to_assembly".
 *
 * The followings are the available columns in table 'tbl_task_to_assembly':
 * @property string $id
 * @property string $task_id
 * @property integer $assembly_id
 * @property integer $sub_assembly_id
 * @property string $parent_id
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property Assembly $assembly
 * @property TaskToAssembly $parent
 * @property TaskToAssembly[] $taskToAssemblies
 * @property SubAssembly $subAssembly
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToMaterial[] $taskToMaterials
 */
class TaskToAssembly extends AdjacencyListActiveRecord
{
	public $standard_id;

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
		return array_merge(parent::rules(), array(
			array('task_id, assembly_id', 'required'),
			array('standard_id', 'safe'),
			array('assembly_id, sub_assembly_id, quantity', 'numerical', 'integerOnly'=>true),
			array('parent_id, task_id', 'length', 'max'=>10),
		));
	}

	public function setCustomValidators()
	{
		if(!empty($this->subAssembly))
		{
			// validate quantity against related assemblyToAssembly record
			$this->rangeModel = $this->subAssembly;
		}
		elseif(!empty($this->taskToAssemblyToAssemblyToAssemblyGroups))
		{
			// validate quantity against related assemblyToAssembly record
			$this->rangeModel = $this->taskToAssemblyToAssemblyToAssemblyGroups[0]->assemblyToAssemblyGroup;
		}
		elseif(!empty($this->taskToAssemblyToTaskTemplateToAssemblyGroups))
		{
			// validate quantity against related assemblyToAssembly record
			$this->rangeModel = $this->taskToAssemblyToTaskTemplateToAssemblyGroups[0]->taskTemplateToAssemblyGroup;
		}
		
		parent::setCustomValidators();
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'parent' => array(self::BELONGS_TO, 'TaskToAssembly', 'parent_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'parent_id'),
            'subAssembly' => array(self::BELONGS_TO, 'SubAssembly', 'sub_assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'task_to_assembly_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'task_to_assembly_id'),
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
			'searchTotalQuantity' => 'Accumlated total',
			'searchAssemblyGroup' => 'Group',
		));
	}

	static function getDisplayAttr()
	{
		return array(
			'searchAssemblyDescription',
			'searchAssemblyAlias',
		);
	}

	public function afterFind() {
		$this->standard_id = $this->assembly->standard_id;
		
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