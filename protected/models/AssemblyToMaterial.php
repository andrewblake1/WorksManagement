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
	public $searchMaterial;
	public $searchUnit;
	public $searchAlias;
	public $searchStage;
	public $searchDetailDrawing;

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
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->composite('searchDetailDrawing', $this->searchDetailDrawing, array(
			'detailDrawing.alias',
			'detailDrawing.description'
		));
		$criteria->compareAs('searchStage', $this->searchStage, 'stage.description', true);
		$criteria->compareAs('searchMaterial', $this->searchMaterial, 'material.description', true);
		$criteria->compareAs('searchUnit', $this->searchUnit, 'material.unit', true);
		$criteria->compareAs('searchAlias', $this->searchAlias, 'material.alias', true);

		$criteria->with = array(
			'material',
			'stage',
			'detailDrawing',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchMaterial';
 		$columns[] = 'searchUnit';
 		$columns[] = 'searchAlias';
 		$columns[] = 'searchStage';
		$columns[] = static::linkColumn('searchDetailDrawing', 'Drawing', 'detail_drawing_id');
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
			'searchMaterial',
			'searchUnit',
			'searchAlias',
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