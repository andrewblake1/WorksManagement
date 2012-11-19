<?php

/**
 * This is the model class for table "assembly_to_material".
 *
 * The followings are the available columns in table 'assembly_to_material':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $material_id
 * @property integer $store_id
 * @property integer $quantity
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Material $store
 * @property Material $material
 * @property Staff $staff
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
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly_to_material';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, material_id, store_id, quantity, staff_id', 'required'),
			array('assembly_id, material_id, store_id, quantity, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, assembly_id, searchMaterialDescription, searchMaterialUnit, searchMaterialAlias, quantity, staff_id', 'safe', 'on'=>'search'),
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
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'store' => array(self::BELONGS_TO, 'Material', 'store_id'),
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'assembly_id' => 'Assembly',
			'material_id' => 'Material/Unit/Alias',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
			'quantity' => 'Quantity',
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
			't.assembly_id',
			't.material_id',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			'material.alias AS searchMaterialAlias',
			't.quantity',
		);

		$criteria->compare('material.description',$this->searchMaterialDescription);
		$criteria->compare('material.unit',$this->searchMaterialUnit);
		$criteria->compare('material.alias',$this->searchMaterialAlias);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		
		$criteria->with = array('material');

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('searchMaterialDescription');
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
 		$columns[] = 'quantity';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchMaterialDescription',
			'searchMaterialUnit',
			'searchMaterialAlias',
		);
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
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->store_id = $assembly->store_id;
		
		return parent::beforeValidate();
	}

}