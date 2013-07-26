<?php

/**
 * This is the model class for table "tbl_task_to_material".
 *
 * The followings are the available columns in table 'tbl_task_to_material':
 * @property string $id
 * @property integer $quantity
 * @property string $task_id
 * @property integer $material_id
 * @property string $task_to_assembly_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property TaskToAssembly $taskToAssembly
 * @property User $updatedBy
 * @property Material $material
 * @property TaskToMaterialToAssemblyToMaterial[] $taskToMaterialToAssemblyToMaterials
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups
 */
class TaskToMaterial extends ActiveRecord
{
	public $standard_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	public $searchStage;
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialGroup;
	public $searchMaterialAlias;
	public $searchAssemblyQuantity;
	public $searchTotalQuantity;

	public function tableName() {

		// need to create the temp table that we will use - required to get the accumlated total - only want to do one shot though hence the atatic
		static $called = false;

		if(!$called && $this->scenario == 'search')
		{
			// generate the temp table used by the view
			Yii::app()->db->createCommand("CALL pro_planning_to_assembly({$_GET['task_id']})")->execute();
			$called = true;
		}

		return ($this->scenario == 'search') || static::$_inSearch
			? 'v_task_to_material'
			: 'tbl_task_to_material';
	}
	
	// needed due to database view
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('standard_id, material_id, task_id', 'required'),
			array('standard_id, material_id, quantity', 'numerical', 'integerOnly'=>true),
			array('task_id, task_to_assembly_id', 'length', 'max'=>10),
		));
	}

	public function setCustomValidators()
	{
		if(!empty($this->taskToMaterialToAssemblyToMaterials))
		{
			// validate quantity against related assemblyToMaterial record
			$this->rangeModel = $this->taskToMaterialToAssemblyToMaterials[0]->assemblyToMaterial;
		}
		elseif(!empty($this->taskToMaterialToAssemblyToMaterialGroups))
		{
			// validate quantity against related assemblyToMaterial record
			$this->rangeModel = $this->taskToMaterialToAssemblyToMaterialGroups[0]->assemblyToMaterialGroup;
		}
		elseif(!empty($this->taskToMaterialToTaskTemplateToMaterialGroups))
		{
			// validate quantity against related assemblyToMaterial record
			$this->rangeModel = $this->taskToMaterialToTaskTemplateToMaterialGroups[0]->taskTemplateToMaterialGroup;
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
            'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'task_to_material_id'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'task_to_material_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'task_to_material_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'material_id' => 'Material',
			'searchTask' => 'Task',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
			'searchAssemblyQuantity' => 'Assembly quantity',
			'search_task_quantity' => 'Task quantity',
			'searchTotalQuantity' => 'Accumlated total',
			'searchMaterialGroup' => 'Group',
			'search_assembly' => 'Assembly',
			'searchStage' => 'Stage',
			'task_id' => 'Task',
			'task_to_assembly_id' => 'Assembly',
		));
	}

	static function getDisplayAttr()
	{
		return array(
			'searchMaterialDescription',
			'searchMaterialUnit',
			'searchMaterialAlias',
		);
	}

	public function afterFind() {
		$this->standard_id = $this->material->standard_id;
		
		return parent::afterFind();
	}
	
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;
		$delimiter = Yii::app()->params['delimiter']['display'];
		
		// update
		if(($this->tableName()) == 'tbl_task_to_material')
		{
			$criteria->select=array(
				't.*',	// needed for delete and update buttons
				'material.description AS searchMaterialDescription',
				'material.unit AS searchMaterialUnit',
				"CONCAT_WS('$delimiter',
					materialToClient.alias,
					material.alias
					) AS searchMaterialAlias",
			);

			$criteria->compare('t.id',$this->id);
			
			// join
			$criteria->join = '
				JOIN tbl_material material ON t.material_id = material.id
				JOIN tbl_task task ON t.task_id = task.id
				JOIN tbl_project project ON task.project_id = project.id
				LEFT JOIN tbl_material_to_client materialToClient ON project.client_id = materialToClient.client_id
					AND t.material_id = materialToClient.material_id
			';

			return $criteria;
		}
		
		// admin
		
		// select
		$criteria->select=array(
			't.*',
			'stage.description AS searchStage',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			't.quantity * task.quantity * taskToAssembly.accumulated_total AS searchTotalQuantity',
			"CONCAT_WS('$delimiter',
				materialToClient.alias,
				material.alias
				) AS searchMaterialAlias",
			"taskToAssembly.accumulated_total AS searchAssemblyQuantity",
			"CONCAT_WS('$delimiter',
				materialGroup.description,
				t.comment
			) AS searchMaterialGroup",
		);
		
		// join
		$criteria->join = '
			LEFT JOIN tbl_stage stage ON t.stage_id = stage.id
			LEFT JOIN tbl_material_group materialGroup ON t.material_group_id = materialGroup.id
			LEFT JOIN tbl_material material ON t.material_id = material.id
			LEFT JOIN tmp_planning_to_assembly taskToAssembly ON t.task_to_assembly_id = taskToAssembly.id
			LEFT JOIN tbl_task task ON t.task_id = task.id
			LEFT JOIN tbl_project project ON task.project_id = project.id
			LEFT JOIN tbl_material_to_client materialToClient ON project.client_id = materialToClient.client_id
				AND t.material_id = materialToClient.material_id
		';
		
		// where
		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$this->compositeCriteria($criteria,
			array(
				'materialToClient.alias',
				'material.alias'
			),
			$this->searchMaterialAlias
		);
		$criteria->compare('t.search_assembly',$this->search_assembly,true);
		$criteria->compare('stage.description',$this->searchStage,true);
		$this->compositeCriteria($criteria,
			array(
			'materialGroup.description',
			't.comment'
			),
			$this->searchMaterialGroup
		);
		$criteria->compare('t.task_to_assembly_id',$this->task_to_assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.search_task_quantity',$this->search_task_quantity);
		$criteria->compare('t.searchAssemblyQuantity',$this->searchAssemblyQuantity);
		$criteria->compare('t.searchTotalQuantity',$this->searchTotalQuantity);
		$criteria->compare('t.task_id',$this->task_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchMaterialDescription';
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
		$columns[] = 'searchMaterialGroup';
		$columns[] = 'searchStage';
		$columns[] = 'quantity';
		$columns[] = 'search_task_quantity';
		$columns[] = 'searchAssemblyQuantity';
		$columns[] = 'searchTotalQuantity';
		$columns[] = static::linkColumn('search_assembly', 'TaskToAssembly', 'task_to_assembly_id');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchMaterialDescription',
			'searchMaterialUnit',
			'searchMaterialAlias',
			'searchMaterialGroup',
			'searchAssemblyQuantity',
			'searchStage',
			'searchTotalQuantity',
		);
	}

}

?>