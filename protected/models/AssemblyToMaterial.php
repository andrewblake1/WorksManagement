<?php

/**
 * This is the model class for table "tbl_assembly_to_material".
 *
 * The followings are the available columns in table 'tbl_assembly_to_material':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $material_id
 * @property integer $stage_id
 * @property integer $standard_id
 * @property integer $detail_drawing_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Drawing $detailDrawing
 * @property Assembly $assembly
 * @property Material $standard
 * @property Material $material
 * @property User $updatedBy
 * @property Stage $stage
 * @property TaskToMaterialToAssemblyToMaterial[] $taskToMaterialToAssemblyToMaterials
 */
class AssemblyToMaterial extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialAlias;
	public $searchStage;
	public $searchDetailDrawingDescription;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'detailDrawing' => array(self::BELONGS_TO, 'Drawing', 'detail_drawing_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'standard' => array(self::BELONGS_TO, 'Material', 'standard_id'),
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'stage' => array(self::BELONGS_TO, 'Stage', 'stage_id'),
            'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'assembly_to_material_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'assembly_id' => 'Assembly',
			'material_id' => 'Material/Unit/Alias/Stage',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
			'stage_id' => 'Stage',
			'searchStage' => 'Stage',
			'searchDetailDrawingDescription' => 'Detail drawing',
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
			't.id',	// needed for delete and update buttons
			't.assembly_id',
			'stage.description AS searchStage',
			'material.description AS searchMaterialDescription',
			"CONCAT_WS('$delimiter',
				detailDrawing.alias,
				detailDrawing.description
				) AS searchDetailDrawingDescription",
			't.detail_drawing_id',
			'material.unit AS searchMaterialUnit',
			'material.alias AS searchMaterialAlias',
			't.material_id',
			't.quantity',
			't.minimum',
			't.maximum',
			't.select',
			't.quantity_tooltip',
		);
		$this->compositeCriteria($criteria,
			array(
				'detailDrawing.alias',
				'detailDrawing.description',
			),
			$this->searchDetailDrawingDescription
		);

		$criteria->compare('stage.description',$this->searchStage,true);
		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$criteria->compare('material.alias',$this->searchMaterialAlias,true);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.select',$this->select,true);

		$criteria->with = array(
			'material',
			'stage',
			'detailDrawing',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchMaterialDescription';
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
 		$columns[] = 'searchStage';
		$columns[] = static::linkColumn('searchDetailDrawingDescription', 'Drawing', 'detail_drawing_id');
 		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'quantity_tooltip';
 		$columns[] = 'select';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchMaterialDescription',
			'searchMaterialUnit',
			'searchMaterialAlias',
			'searchStage',
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->standard_id = $assembly->standard_id;
		
		return parent::beforeValidate();
	}

}