<?php

/**
 * This is the model class for table "tbl_client_to_material".
 *
 * The followings are the available columns in table 'tbl_client_to_material':
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
class ClientToMaterial extends ActiveRecord
{
	public $searchMaterial;
	public $searchUnit;
	public $searchAlias;
	public $searchSupplier;
	
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
	public function attributeLabels($attributeLabels = array())
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
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchMaterial', $this->searchMaterial, 'material.description', true);
		$criteria->compareAs('searchUnit', $this->searchUnit, 'material.unit', true);
		$criteria->compareAs('searchAlias', $this->searchAlias, 'material.alias', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);

		$criteria->with = array(
			'material',
			'supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = array(
			'class'=>'WMTbImageColumn',
			'imagePathExpression'=>'$data->material->exposeSingle()',
			'usePlaceKitten'=>FALSE,
		);
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