<?php

/**
 * This is the model class for table "tbl_material_to_client".
 *
 * The followings are the available columns in table 'tbl_material_to_client':
 * @property integer $id
 * @property integer $material_id
 * @property integer $client_id
 * @property integer $supplier_id
 * @property string $unit_price
 * @property string $alias
 * @property string $category
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Material $material
 * @property Client $client
 * @property User $updatedBy
 * @property Supplier $supplier
 */
class MaterialToClient extends ActiveRecord
{
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialAlias;
	public $searchSupplierName;

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
		return array_merge(parent::rules(), array(
			array('material_id, client_id, supplier_id', 'required'),
			array('material_id, client_id, supplier_id', 'numerical', 'integerOnly'=>true),
			array('unit_price', 'length', 'max'=>7),
			array('alias', 'length', 'max'=>255),
            array('category', 'safe'),
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
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'unit_price' => 'Unit price',
			'material_id' => 'Material/Unit/Alias',
			'client_id' => 'Client',
			'alias' => 'Client alias',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
			'searchSupplierName' => 'Supplier',
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
			't.material_id',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			'material.alias AS searchMaterialAlias',
			'supplier.name AS searchSupplierName',
			't.unit_price',
			"t.alias",
			't.client_id',
			't.category',
		);

		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$criteria->compare('material.alias',$this->searchMaterialAlias,true);
		$criteria->compare('supplier.name',$this->searchSupplierName,true);
		$criteria->compare('t.alias',$this->alias,true);
		$criteria->compare('t.category',$this->category,true);
 		$criteria->compare('t.client_id',$this->client_id);
		$criteria->compare('t.unit_price', $this->unit_price);

		$criteria->with = array(
			'material',
			'supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchMaterialDescription';
 		$columns[] = 'category';
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
 		$columns[] = 'searchSupplierName';
 		$columns[] = 'alias';
		$columns[] = 'unit_price';

		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'material->description',
			'material->unit',
			'material->alias',
			'alias',
		);
	}

}

?>