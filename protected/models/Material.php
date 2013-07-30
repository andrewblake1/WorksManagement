<?php

/**
 * This is the model class for table "tbl_material".
 *
 * The followings are the available columns in table 'tbl_material':
 * @property integer $id
 * @property integer $standard_id
 * @property string $description
 * @property string $category
 * @property string $unit
 * @property string $alias
 * @property integer $drawing_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterial[] $assemblyToMaterials1
 * @property User $updatedBy
 * @property Drawing $standard
 * @property Drawing $drawing
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property MaterialToClient[] $materialToClients
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskToMaterial[] $taskToMaterials
 */
class Material extends ActiveRecord
{
	use FileActiveRecordTrait;

	public $searchDrawing;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'standard_id'),
            'assemblyToMaterials1' => array(self::HAS_MANY, 'AssemblyToMaterial', 'material_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Drawing', 'standard_id'),
            'drawing' => array(self::BELONGS_TO, 'Drawing', 'drawing_id'),
            'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'material_id'),
            'materialToClients' => array(self::HAS_MANY, 'MaterialToClient', 'material_id'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'material_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'material_id'),
        );
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
			't.unit',
			't.category',
			't.drawing_id',
			"CONCAT_WS('$delimiter',
				drawing.alias,
				drawing.description
				) AS searchDrawing",
		);

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.description', $this->description,true);
		$criteria->compare('t.alias', $this->alias,true);
		$criteria->compare('t.unit', $this->unit);
		$criteria->compare('t.standard_id', $this->standard_id);
		$criteria->compare('t.category',$this->category,true);
		$this->compositeCriteria($criteria,
			array(
				'drawing.alias',
				'drawing.description',
			),
			$this->searchDrawing
		);

		$criteria->with = array(
			'drawing',
		);
		
		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->imageColumn();
		$columns[] = 'description';
		$columns[] = 'alias';
 		$columns[] = 'category';
		$columns[] = 'unit';
		$columns[] = static::linkColumn('searchDrawing', 'Drawing', 'drawing_id');

		return $columns;
	}
	
	/**
	 * @return	array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			't.alias',
			't.description',
		);
	}

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('t.standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeMaterialGroup($material_group_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('materialGroupToMaterial.material_group_id', $material_group_id);

		// join
		$criteria->join = '
			JOIN tbl_material_group_to_material materialGroupToMaterial ON materialGroupToMaterial.material_id = t.id
		';

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>