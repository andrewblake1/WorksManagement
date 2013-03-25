<?php

/**
 * This is the model class for table "material_group_to_material".
 *
 * The followings are the available columns in table 'material_group_to_material':
 * @property integer $id
 * @property integer $material_group_id
 * @property integer $material_id
 * @property integer $store_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property MaterialGroup $materialGroup
 * @property Material $store
 * @property Material $material
 * @property Staff $staff
 * @property TaskToMaterialToMaterialGroupToMaterial[] $taskToMaterialToMaterialGroupToMaterials
 * @property TaskToMaterialToMaterialGroupToMaterial[] $taskToMaterialToMaterialGroupToMaterials1
 */
class MaterialGroupToMaterial extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterial;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('material_id, material_group_id, store_id,', 'required'),
			array('material_id, material_group_id, store_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchMaterial, material_id, material_group_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
			'store' => array(self::BELONGS_TO, 'Material', 'store_id'),
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToMaterialToMaterialGroupToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToMaterialGroupToMaterial', 'material_id'),
			'taskToMaterialToMaterialGroupToMaterials1' => array(self::HAS_MANY, 'TaskToMaterialToMaterialGroupToMaterial', 'material_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'material_group_id' => 'Material Group',
			'material_id' => 'Material',
			'searchMaterial' => 'Material',
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
			'material.description AS searchMaterial',
		);

		// where
		$criteria->compare('material_group_id',$this->material_group_id);
		$criteria->compare('material.description',$this->searchMaterial);

		// join
		$criteria->with = array(
			'material',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchMaterial', 'Material', 'material_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'material->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchMaterial');
	}
	
	public function beforeValidate()
	{
		$materialGroup = MaterialGroup::model()->findByPk($this->material_group_id);
		$this->store_id = $materialGroup->store_id;
		
		return parent::beforeValidate();
	}

}