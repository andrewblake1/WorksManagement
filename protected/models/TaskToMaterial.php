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
	use RangeActiveRecordTrait;

	public $standard_id;

	public $searchStage;
	public $searchMaterial;
	public $searchUnit;
	public $searchGroup;
	public $searchAlias;
	public $searchAssemblyQuantity;
	public $searchAccumlatedTotal;

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
		return array_merge(parent::rules(), array(
			array('standard_id', 'required'),
			array('standard_id', 'numerical', 'integerOnly'=>true),
		));
	}

	public function setCustomValidators()
	{
		$rangeModel = null;
		
		if(!empty($this->taskToMaterialToAssemblyToMaterials))
		{
			// validate quantity against related assemblyToMaterial record
			$rangeModel = $this->taskToMaterialToAssemblyToMaterials[0]->assemblyToMaterial;
		}
		elseif(!empty($this->taskToMaterialToAssemblyToMaterialGroups))
		{
			// validate quantity against related assemblyToMaterial record
			$rangeModel = $this->taskToMaterialToAssemblyToMaterialGroups[0]->assemblyToMaterialGroup;
		}
		elseif(!empty($this->taskToMaterialToTaskTemplateToMaterialGroups))
		{
			// validate quantity against related assemblyToMaterial record
			$rangeModel = $this->taskToMaterialToTaskTemplateToMaterialGroups[0]->taskTemplateToMaterialGroup;
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
            'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'task_to_material_id'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'task_to_material_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'task_to_material_id'),
        );
    }

	static function getDisplayAttr()
	{
		return array(
			'searchMaterial',
			'searchUnit',
			'searchAlias',
		);
	}

	public function afterFind() {
		if($this->id)
		{
			$this->standard_id = $this->material->standard_id;
		}
		
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
				'material.description AS searchMaterial',
				'material.unit AS searchUnit',
				"CONCAT_WS('$delimiter',
					materialToClient.alias,
					material.alias
					) AS searchAlias",
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
			'material.description AS searchMaterial',
			'material.unit AS searchUnit',
			't.quantity * task.quantity * taskToAssembly.accumulated_total AS searchAccumlatedTotal',
			"CONCAT_WS('$delimiter',
				materialToClient.alias,
				material.alias
				) AS searchAlias",
			"taskToAssembly.accumulated_total AS searchAssemblyQuantity",
			"CONCAT_WS('$delimiter',
				materialGroup.description,
				t.comment
			) AS searchGroup",
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
		$criteria->compare('material.description',$this->searchMaterial,true);
		$criteria->compare('material.unit',$this->searchUnit,true);
		$this->compositeCriteria($criteria,
			array(
				'materialToClient.alias',
				'material.alias'
			),
			$this->searchAlias
		);
		$criteria->compare('t.search_assembly',$this->search_assembly,true);
		$criteria->compare('stage.description',$this->searchStage,true);
		$this->compositeCriteria($criteria,
			array(
			'materialGroup.description',
			't.comment'
			),
			$this->searchGroup
		);
		$criteria->compare('t.task_to_assembly_id',$this->task_to_assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.search_task_quantity',$this->search_task_quantity);
		$criteria->compare('t.searchAssemblyQuantity',$this->searchAssemblyQuantity);
		$criteria->compare('t.searchAccumlatedTotal',$this->searchAccumlatedTotal);
		$criteria->compare('t.task_id',$this->task_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchMaterial';
 		$columns[] = 'searchUnit';
 		$columns[] = 'searchAlias';
		$columns[] = 'searchGroup';
		$columns[] = 'searchStage';
		$columns[] = 'quantity';
		$columns[] = 'search_task_quantity';
		$columns[] = 'searchAssemblyQuantity';
		$columns[] = 'searchAccumlatedTotal';
		$columns[] = static::linkColumn('search_assembly', 'TaskToAssembly', 'task_to_assembly_id');
		
		return $columns;
	}

}

?>