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
class TaskToAssembly extends ActiveRecord
{
	use AdjacencyListActiveRecordTrait;
	use RangeActiveRecordTrait;
	
	public $standard_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	
	public $searchAssemblyDescription;
	public $searchAssemblyGroup;
	public $searchAssemblyAlias;
	public $searchTaskQuantity;
	public $searchTotalQuantity;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('standard_id', 'numerical', 'integerOnly'=>true),
		));
	}

	public function tableName() {

		// need to create the temp table that we will use - required to get the accumlated total - only want to do one shot though hence the atatic
		static $called = false;

		if(!$called && $this->scenario == 'search')
		{
			// generate the temp table used by the view
			Yii::app()->db->createCommand("CALL pro_planning_to_assembly({$_GET['task_id']})")->execute();
			$called = true;
		}

		return ($this->scenario == 'search') || static::$inSearch
			? 'tmp_planning_to_assembly'
			: 'tbl_task_to_assembly';
	}
	
	// needed due to database view
	public function primaryKey()
	{
		return 'id';
	}

	public function setCustomValidators()
	{
		$rangeModel = NULL;
	
		if(!empty($this->subAssembly))
		{
			// validate quantity against related assemblyToAssembly record
			$rangeModel = $this->subAssembly;
		}
		elseif(!empty($this->taskToAssemblyToAssemblyToAssemblyGroups))
		{
			// validate quantity against related assemblyToAssembly record
			$rangeModel = $this->taskToAssemblyToAssemblyToAssemblyGroups[0]->assemblyToAssemblyGroup;
		}
		elseif(!empty($this->taskToAssemblyToTaskTemplateToAssemblyGroups))
		{
			// validate quantity against related assemblyToAssembly record
			$rangeModel = $this->taskToAssemblyToTaskTemplateToAssemblyGroups[0]->taskTemplateToAssemblyGroup;
		}
		
		$this->setCustomValidatorsFromSource($rangeModel);
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
		if($this->id)
		{
			$this->standard_id = $this->assembly->standard_id;
		}
		
		return parent::afterFind();
	}
	
	/*
	 * to be overidden if using mulitple models
	 */
	public function createSave(&$models=array())
	{
		return TaskToAssemblyController::addAssembly($this->task_id, $this->assembly_id, $this->quantity, $this->parent_id, $this->sub_assembly_id, $models, $this);
	}
	
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;
		$delimiter = Yii::app()->params['delimiter']['display'];
		
		// update
		if(($this->tableName()) == 'tbl_task_to_assembly')
		{
			$criteria->select=array(
				't.*',	// needed for delete and update buttons
				'assembly.description AS searchAssemblyDescription',
				"CONCAT_WS('$delimiter',
					assemblyToClient.alias,
					assembly.alias
					) AS searchAssemblyAlias",
			);

			$criteria->compare('t.id',$this->id);

			// join
			$criteria->join = '
				JOIN tbl_assembly assembly ON t.assembly_id = assembly.id
				JOIN tbl_task task ON t.task_id = task.id
				JOIN tbl_project project ON task.project_id = project.id
				LEFT JOIN tbl_assembly_to_client assemblyToClient ON project.client_id = assemblyToClient.client_id
					AND t.assembly_id = assemblyToClient.assembly_id
			';

			return $criteria;
		}
		
		// admin
		
		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.task_id',
			't.parent_id',
			't.assembly_id',
			'assembly.description AS searchAssemblyDescription',
			"CONCAT_WS('$delimiter',
				assemblyToClient.alias,
				assembly.alias
				) AS searchAssemblyAlias",
			't.quantity',
			'task.quantity AS searchTaskQuantity',
			't.accumulated_total * task.quantity AS searchTotalQuantity',
			't.assembly_group_to_assembly_id',
			't.assembly_group_id',
			't.task_to_assembly_to_assembly_to_assembly_group_id',
			't.assembly_to_assembly_group_id',

			't.task_to_assembly_to_task_template_to_assembly_group_id',
			't.task_template_to_assembly_group_id',
	
			"CONCAT_WS('$delimiter',
				assemblyGroup.description,
				t.comment
				) AS searchAssemblyGroup",
		);
				
		// join
		$criteria->join = '
			LEFT JOIN tbl_assembly_group assemblyGroup ON t.assembly_group_id = assemblyGroup.id
			LEFT JOIN tbl_assembly assembly ON t.assembly_id = assembly.id
			LEFT JOIN tbl_task task ON t.task_id = task.id
			LEFT JOIN tbl_project project ON task.project_id = project.id
			LEFT JOIN tbl_assembly_to_client assemblyToClient ON project.client_id = assemblyToClient.client_id
				AND t.assembly_id = assemblyToClient.assembly_id
		';
		
		// where
		$criteria->compare('assembly.description',$this->searchAssemblyDescription,true);
		$this->compositeCriteria($criteria,
			array(
				'assemblyToClient.alias',
				'assembly.alias'
			),
			$this->searchAssemblyAlias
		);
		$this->compositeCriteria($criteria,
			array(
				'assemblyGroup.description',
				't.comment'
			),
			$this->searchAssemblyGroup
		);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('task.quantity',$this->searchTaskQuantity);
		$criteria->compare('t.searchTotalQuantity',$this->searchTotalQuantity);
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.task_id',$this->task_id);
		$criteria->compare('t.parent_id',$this->parent_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
 		$columns[] = 'parent_id';
		$columns[] = 'searchAssemblyDescription';
 		$columns[] = 'searchAssemblyAlias';
		$columns[] = 'searchAssemblyGroup';
		$columns[] = 'quantity';
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'searchTotalQuantity';

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchAssemblyDescription',
			'searchAssemblyAlias',
			'searchAssemblyGroup',
			'parent_id',
			'quantity',
			'searchTaskQuantity',
			'searchTotalQuantity',
		);
	}
	
}

?>