<?php

/**
 * This is the model class for table "material_to_client".
 *
 * The followings are the available columns in table 'material_to_client':
 * @property integer $id
 * @property integer $material_id
 * @property integer $client_id
 * @property string $alias
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
			array('alias', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, searchMaterial, alias, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'material_id' => 'Material',
			'client_id' => 'Client',
			'alias' => 'Alias',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->select=array(
			"CONCAT_WS('$delimiter',
				material.description,
				material.alias
				) AS searchMaterial",
			't.alias',
			't.client_id',
		);

		$this->compositeCriteria($criteria,
			array(
			'material.description',
			'material.alias'
			),
			$this->searchMaterial
		);
		$criteria->compare('t.client_id',$this->client_id,true);
		$criteria->compare('t.alias',$this->alias);

		$criteria->with = array('material');

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchMaterial', 'Material', 'assembly_id');
 		$columns[] = 'alias';

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchMaterial');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'material->description',
			'material->alias',
		);
	}

}

?>