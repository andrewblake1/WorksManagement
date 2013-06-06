<?php

class ViewTaskToAssembly extends ViewActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyDescription;
	public $searchAssemblyGroup;
	public $searchAssemblyAlias;
	public $searchTaskQuantity;
	public $searchTotalQuantity;

	protected $defaultSort = array('searchAssemblyGroup'=>'DESC', 't.parent_id', 'searchAssemblyGroup');
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, task_id, parent_id, quantity, searchTotalQuantity, searchTaskQuantity, searchAssemblyAlias, searchAssemblyGroup, searchMaterialDescription', 'safe', 'on'=>'search'),
		);
	}

	public function tableName() {

		// need to create the temp table that we will use - required to get the accumlated total - only want to do one shot though hence the atatic
		static $tableName = NULL;
		if(!$tableName)
		{
			Yii::app()->db->createCommand("CALL pro_planning_to_assembly({$_GET['task_id']})")->execute();
		}

		return $tableName = 'tmp_planning_to_assembly';
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
			't.search_task_to_assembly_to_assembly_to_assembly_group_id',
			't.assembly_to_assembly_group_id',
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
		$columns[] = 'searchAssemblyGroup';
		$columns[] = 'quantity';
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'searchTotalQuantity';
 		$columns[] = 'searchAssemblyDescription';
 		$columns[] = 'searchAssemblyAlias';

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

	static function getDisplayAttr()
	{
		return TaskToAssembly::getDisplayAttr();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return TaskToAssembly::model()->attributeLabels();
	}
}

?>