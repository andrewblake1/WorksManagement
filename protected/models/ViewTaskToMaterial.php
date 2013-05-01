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
	public $searchTaskQuantity;
	public $searchTotalQuantity;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_task_to_material';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, task_id, task_to_assembly_id, searchMaterialUnit, searchTotalQuantity, searchTaskQuantity, searchMaterialAlias, searchMaterialGroup, searchStage, searchMaterialDescription, searchAssembly, quantity', 'safe', 'on'=>'search'),
		);
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
			't.searchTaskQuantity',
			't.searchTotalQuantity',
			"CONCAT_WS('$delimiter',
				materialToClient.alias,
				material.alias
				) AS searchMaterialAlias",
			"IF(taskToAssembly.quantity IS NOT NULL, CONCAT_WS(' * ', t.quantity, taskToAssembly.quantity), t.quantity) AS quantity",
			't.material_group_to_material_id',
			't.searchAssembly',
			't.searchAssemblyId',
			't.searchTaskToMaterialToAssemblyToMaterialGroupId',
			't.assembly_to_material_group_id',
			"CONCAT_WS('$delimiter',
				materialGroup.description,
				t.comment
			) AS searchMaterialGroup",
		);
		
		// join
		$criteria->join = '
			LEFT JOIN stage on t.stage_id = stage.id
			LEFT JOIN material_group materialGroup ON t.material_group_id = materialGroup.id
			LEFT JOIN material ON t.material_id = material.id
			LEFT JOIN task_to_assembly taskToAssembly ON t.task_to_assembly_id = taskToAssembly.id
			LEFT JOIN task ON t.task_id = task.id
			LEFT JOIN project on task.project_id = project.id
			LEFT JOIN material_to_client materialToClient ON project.client_id = materialToClient.client_id
				AND t.material_id = materialToClient.material_id
		';
		
		// where
		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$this->compositeCriteria($criteria,
			array(
				'material_to_client.alias',
				'material.alias'
			),
			$this->searchMaterialAlias
		);
		$criteria->compare('searchAssembly',$this->searchAssembly,true);
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
		$criteria->compare('t.searchTaskQuantity',$this->searchTaskQuantity);
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
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'searchTotalQuantity';
		$columns[] = static::linkColumn('searchAssembly', 'TaskToAssembly', 'task_to_assembly_id');
		
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
			'searchAssembly',
			'searchStage',
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