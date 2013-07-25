<?php

/**
 * This is the model class for table "tbl_assembly".
 *
 * The followings are the available columns in table 'tbl_assembly':
 * @property integer $id
 * @property integer $standard_id
 * @property string $description
 * @property string $alias
 * @property integer $drawing_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Drawing $standard
 * @property Drawing $drawing
 * @property AssemblyGroupToAssembly[] $assemblyGroupToAssemblies
 * @property AssemblyGroupToAssembly[] $assemblyGroupToAssemblies1
 * @property AssemblyToAssemblyGroup[] $assemblyToAssemblyGroups
 * @property AssemblyToClient[] $assemblyToClients
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property SubAssembly[] $subAssemblies
 * @property SubAssembly[] $subAssemblies1
 * @property SubAssembly[] $subAssemblies2
 * @property TaskTemplateToAssembly[] $taskTemplateToAssemblies
 * @property TaskToAssembly[] $taskToAssemblies
 */
class Assembly extends ActiveRecord
{
	protected $defaultSort = array('t.description');
	
	public $searchDrawingDescription;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description, standard_id', 'required'),
			array('drawing_id, standard_id', 'numerical', 'integerOnly'=>true),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Drawing', 'standard_id'),
            'drawing' => array(self::BELONGS_TO, 'Drawing', 'drawing_id'),
            'assemblyGroupToAssemblies' => array(self::HAS_MANY, 'AssemblyGroupToAssembly', 'standard_id'),
            'assemblyGroupToAssemblies1' => array(self::HAS_MANY, 'AssemblyGroupToAssembly', 'assembly_id'),
            'assemblyToAssemblyGroups' => array(self::HAS_MANY, 'AssemblyToAssemblyGroup', 'assembly_id'),
            'assemblyToClients' => array(self::HAS_MANY, 'AssemblyToClient', 'assembly_id'),
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'assembly_id'),
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'assembly_id'),
            'subAssemblies' => array(self::HAS_MANY, 'SubAssembly', 'parent_assembly_id'),
            'subAssemblies1' => array(self::HAS_MANY, 'SubAssembly', 'standard_id'),
            'subAssemblies2' => array(self::HAS_MANY, 'SubAssembly', 'child_assembly_id'),
            'taskTemplateToAssemblies' => array(self::HAS_MANY, 'TaskTemplateToAssembly', 'assembly_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'assembly_id'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'searchDrawingDescription' => 'Drawing',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.description',
			't.alias',
			't.drawing_id',
			"CONCAT_WS('$delimiter',
				drawing.alias,
				drawing.description
				) AS searchDrawingDescription",
		);
		
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.alias',$this->alias,true);
		$criteria->compare('t.standard_id',$this->standard_id);
		$criteria->compare('drawing.description',$this->searchDrawingDescription,true);
		$this->compositeCriteria($criteria,
			array(
				'drawing.alias',
				'drawing.description',
			),
			$this->searchDrawingDescription
		);

		$criteria->with=array(
			'drawing',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'description';
		$columns[] = 'alias';
		$columns[] = static::linkColumn('searchDrawingDescription', 'Drawing', 'drawing_id');
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			't.id',
			't.alias',
			't.description',
		);
	}
 
	public function scopeAssemblyGroup($assembly_group_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('assemblyGroupToAssembly.assembly_group_id', $assembly_group_id);

		// join
		$criteria->join = '
			JOIN tbl_assembly_group_to_assembly assemblyGroupToAssembly ON assemblyGroupToAssembly.assembly_id = t.id
		';

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('t.standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>