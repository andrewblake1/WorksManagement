<?php

class ViewTaskToAssembly extends ViewActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyGroup;
	public $searchAssembly;
	protected $defaultSort = array('searchAssemblyGroup'=>'DESC', 't.parent_id', 'searchAssembly');

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_task_to_assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, task_id, parent_id, quantity, searchAssemblyGroup, searchAssembly', 'safe', 'on'=>'search'),
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
			't.task_id',
			't.parent_id',
			't.assembly_id',
			'assembly.description AS searchAssembly',
			't.quantity',
			't.assembly_group_id',
			't.searchTaskToAssemblyToAssemblyGroupToAssemblyId',
			't.assembly_to_assembly_group_id',
			"CONCAT_WS('$delimiter',
					assemblyGroup.description,
					t.searchAssemblyToAssemblyGroupComment
					) AS searchAssemblyGroup",
				);
				
		// join
		$criteria->join = '
			LEFT JOIN assembly_group assemblyGroup ON t.assembly_group_id = assemblyGroup.id
			LEFT JOIN assembly ON t.assembly_id = assembly.id
		';
		
		// where
		$criteria->compare('assembly.description',$this->searchAssembly,true);
		$this->compositeCriteria($criteria,
			array(
			'assemblyGroup.description',
			't.searchAssemblyToAssemblyGroupComment'
			),
			$this->searchAssemblyGroup
		);
		$criteria->compare('t.quantity',$this->quantity);
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
        $this->linkColumnAdjacencyList('searchAssembly', $columns);

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchAssembly',
			'searchAssemblyGroup',
			'parent_id',
			'quantity',
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