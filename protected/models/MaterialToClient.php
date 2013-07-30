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
	public $searchMaterial;
	public $searchUnit;
	public $searchAlias;
	public $searchSupplier;

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
			'alias' => 'Client alias',
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
			'material.description AS searchMaterial',
			'material.unit AS searchUnit',
			'material.alias AS searchAlias',
			'supplier.name AS searchSupplier',
			't.unit_price',
			"t.alias",
			't.client_id',
		);

		$criteria->compare('material.description',$this->searchMaterial,true);
		$criteria->compare('material.unit',$this->searchUnit,true);
		$criteria->compare('material.alias',$this->searchAlias,true);
		$criteria->compare('supplier.name',$this->searchSupplier,true);
		$criteria->compare('t.alias',$this->alias,true);
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
 		$columns[] = 'searchMaterial';
 		$columns[] = 'category';
 		$columns[] = 'searchUnit';
 		$columns[] = 'searchAlias';
 		$columns[] = 'searchSupplier';
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
			'searchMaterial',
			'searchUnit',
			'searchAlias',
			't.alias',
		);
	}

}

?>