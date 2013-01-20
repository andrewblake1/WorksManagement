<?php

/**
 * This is the model class for table "material_to_client".
 *
 * The followings are the available columns in table 'material_to_client':
 * @property integer $id
 * @property integer $material_id
 * @property integer $client_id
 * @property string $alias
 * @property string $unit_price
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Material $material
 * @property Client $client
 * @property Staff $staff
 */
class MaterialToClient extends ActiveRecord
{
	public $searchAlias;
	public $searchMaterial;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'material_to_client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('material_id, client_id, staff_id', 'required'),
			array('material_id, client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('unit_price', 'length', 'max'=>7),
			array('alias', 'length', 'max'=>255),
			array('id, client_id, searchMaterial, searchAlias, alias, unit_price, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Material',
			'unit_price' => 'Unit price',
			'material_id' => 'Material',
			'client_id' => 'Client',
			'alias' => 'Client alias/Material alias',
			'searchAlias' => 'Client alias/Material alias',
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
			't.id',	// needed for delete and update buttons
			'material.description AS searchMaterial',
			't.material_id',
			't.unit_price',
			"CONCAT_WS('$delimiter',
				t.alias,
				material.alias
				) AS searchAlias",
			't.client_id',
		);

		$this->compositeCriteria($criteria,
			array(
				't.alias',
				'material.alias',
			),
			$this->searchAlias
		);
		$criteria->compare('searchMaterial',$this->searchMaterial,true);
		$criteria->compare('t.client_id',$this->client_id,true);
		$criteria->compare('t.unit_price', $this->unit_price);

		$criteria->with = array('material');

		return $criteria;
	}

	public function getAdminColumns()
	{
 //       $columns[] = static::linkColumn('searchMaterial', 'Material', 'material_id');
		$columns[] = $this->linkThisColumn('searchMaterial');
        $columns[] = 'searchAlias';
		$columns[] = 'unit_price';

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAlias', 'searchMaterial');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'material->description',
			'alias',
			'material->alias',
		);
	}

}

?>