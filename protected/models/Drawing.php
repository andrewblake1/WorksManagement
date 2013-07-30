<?php

/**
 * This is the model class for table "tbl_drawing".
 *
 * The followings are the available columns in table 'tbl_drawing':
 * @property integer $id
 * @property integer $standard_id
 * @property string $description
 * @property string $alias
 * @property integer $default_order
 * @property integer $parent_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Assembly[] $assemblies
 * @property Assembly[] $assemblies1
 * @property AssemblyToAssemblyGroup[] $assemblyToAssemblyGroups
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property User $updatedBy
 * @property Standard $standard
 * @property Drawing $parent
 * @property Drawing[] $drawings
 * @property Material[] $materials
 * @property Material[] $materials1
 * @property SubAssembly[] $subAssemblies
 */
class Drawing extends ActiveRecord
{
	use  AdjacencyListActiveRecordTrait;
	use  FileActiveRecordTrait;

	protected $defaultSort = array('t.default_order');
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblies' => array(self::HAS_MANY, 'Assembly', 'standard_id'),
            'assemblies1' => array(self::HAS_MANY, 'Assembly', 'drawing_id'),
            'assemblyToAssemblyGroups' => array(self::HAS_MANY, 'AssemblyToAssemblyGroup', 'detail_drawing_id'),
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'detail_drawing_id'),
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'detail_drawing_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Standard', 'standard_id'),
            'parent' => array(self::BELONGS_TO, 'Drawing', 'parent_id'),
            'drawings' => array(self::HAS_MANY, 'Drawing', 'parent_id'),
            'materials' => array(self::HAS_MANY, 'Material', 'standard_id'),
            'materials1' => array(self::HAS_MANY, 'Material', 'drawing_id'),
            'subAssemblies' => array(self::HAS_MANY, 'SubAssembly', 'detail_drawing_id'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'default_order' => 'Default order no.',
		));
	}

	public static function getDisplayAttr()
	{
		return array(
			't.id',
			't.description',
			't.alias',
		);
	}
 
// TODO: probably breaking mvc here again calling controller code	
	public function linkColumnAdjacencyList($name, &$columns, $primaryKeyName = 'id', $parentAttrib = 'parent_id')
	{
		$modelName = str_replace('View', '', get_class($this));
		$controllerName = "{$modelName}Controller";

		// add addtional columns for managment of the adjacency list if user has write access
		if($controllerName::checkAccess(Controller::accessWrite))
		{
			if(!is_array($columns) || !in_array($primaryKeyName, $columns))
			{
				$columns[] = $primaryKeyName;
			}
			if(!in_array($parentAttrib, $columns))
			{
				$columns[] = $parentAttrib;
			}
		}

		// if the user has at least read access
		if($controllerName::checkAccess(Controller::accessRead))
		{
			// NB: want id intead of $this->tableSchema->primaryKey because yii wants a variable by the same as in the function signature
			// even though this confusing here
			// create a link
			$params = var_export(Controller::getAdminParams($modelName), true);
			$columns[] = array(
				'name'=>$name,
				'value'=> 'Drawing::model()->findByAttributes(array("'.$parentAttrib.'" => $data->'.$primaryKeyName.')) !== null
					? CHtml::link($data->'.$name.', Yii::app()->createUrl("'."$modelName/admin".'", array("'.$parentAttrib.'"=>$data->'.$primaryKeyName.') + '.$params.'))
					: $data->'.$name,
				'type'=>'raw',
			);
		}
		else
		{
			// create text
			$columns[] = $name;
		}
	}

	public function getAdminColumns()
	{
 		// link to admin displaying children or if no children then just description without link
        $this->linkColumnAdjacencyList('description', $columns);
		$columns[] = 'alias';
		
		return $columns;
	}

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
}

?>