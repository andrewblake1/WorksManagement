<?php

class ViewTaskToMaterial extends ViewActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchStage;
	public $searchMaterialGroup;

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
			array('id, task_id, task_to_assembly_id, searchMaterialGroup, searchStage, searchMaterial, searchAssembly, quantity', 'safe', 'on'=>'search'),
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
			't.material_group_id',
			'stage.description AS searchStage',
			't.task_to_assembly_id',
			'material.description AS searchMaterial',
			't.quantity',
			't.searchAssembly',
			't.searchAssemblyId',
			't.searchTaskToMaterialToMaterialGroupToMaterialId',
			't.assembly_to_material_group_id',
			"CONCAT_WS('$delimiter',
				materialGroup.description,
				t.searchAssemblyToMaterialGroupComment
				) AS searchMaterialGroup",
		);
		
		// join
		$criteria->join = '
			LEFT JOIN stage on t.stage_id = stage.id
			LEFT JOIN material_group materialGroup ON t.material_group_id = materialGroup.id
			LEFT JOIN material ON t.material_id = material.id
		';
		
		// where
		$criteria->compare('material.description',$this->searchMaterial,true);
		$this->compositeCriteria($criteria,
			array(
			'materialGroup.description',
			't.searchAssemblyToMaterialGroupComment'
			),
			$this->searchMaterialGroup
		);
		$criteria->compare('searchAssembly',$this->searchAssembly,true);
		$criteria->compare('stage.description',$this->searchStage,true);
		$criteria->compare('t.task_to_assembly_id',$this->task_to_assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.task_id',$this->task_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = $this->linkThisColumn('searchMaterial');
		$columns[] = 'searchMaterialGroup';
		$columns[] = 'searchStage';
		$columns[] = 'quantity';
		$columns[] = static::linkColumn('searchAssembly', 'Assembly', 'searchAssemblyId');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchMaterial',
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