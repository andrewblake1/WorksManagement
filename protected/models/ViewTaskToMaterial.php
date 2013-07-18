<?php

class ViewTaskToMaterial extends ViewActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchStage;
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialGroup;
	public $searchMaterialAlias;
	public $searchAssemblyQuantity;
	public $searchTotalQuantity;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, task_template_to_material_group_id, task_id, task_to_assembly_id, searchMaterialUnit, searchAssemblyQuantity, searchTotalQuantity, search_task_quantity, searchMaterialAlias, searchMaterialGroup, searchStage, searchMaterialDescription, search_assembly, quantity', 'safe', 'on'=>'search'),
		);
	}

	public function tableName() {

		// need to create the temp table that we will use - required to get the accumlated total - only want to do one shot though hence the atatic
		static $tableName = NULL;
		if(!$tableName)
		{
			Yii::app()->db->createCommand("CALL pro_planning_to_assembly({$_GET['task_id']})")->execute();
		}

		return $tableName = parent::tableName();
	}
	
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.material_id',
			't.task_id',
			't.task_to_assembly_id',
			't.material_group_id',
			'stage.description AS searchStage',
			't.task_to_assembly_id',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			't.search_task_quantity',
			't.quantity * task.quantity * taskToAssembly.accumulated_total AS searchTotalQuantity',
			"CONCAT_WS('$delimiter',
				materialToClient.alias,
				material.alias
				) AS searchMaterialAlias",
			't.quantity',
			't.material_group_to_material_id',
			"taskToAssembly.accumulated_total AS searchAssemblyQuantity",
			't.search_assembly',
			't.search_assembly_id',
			't.task_to_material_to_assembly_to_material_group_id',
			't.assembly_to_material_group_id',

			't.task_to_material_to_task_template_to_material_group_id',
			't.task_template_to_material_group_id',

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

	static function getDisplayAttr()
	{
		return TaskToMaterial::getDisplayAttr();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return TaskToMaterial::model()->attributeLabels();
	}
}

?>