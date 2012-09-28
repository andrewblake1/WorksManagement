<?php

/**
 * This is the model class for table "material".
 *
 * The followings are the available columns in table 'material':
 * @property integer $id
 * @property string $description
 * @property string $unit_price
 * @property string $unit
 * @property string $client_alias
 * @property integer $client_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterial[] $assemblyToMaterials1
 * @property Staff $staff
 * @property Client $client
 * @property MaterialToTask[] $materialToTasks
 * @property TaskTypeToMaterial[] $taskTypeToMaterials
 */
class Material extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'material';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, client_id, staff_id', 'required'),
			array('client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description, client_alias', 'length', 'max'=>255),
			array('unit_price', 'length', 'max'=>7),
			array('unit', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, searchStaff, unit_price, unit, client_alias, client_id, staff_id', 'safe', 'on'=>'search'),
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
			'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'client_id'),
			'assemblyToMaterials1' => array(self::HAS_MANY, 'AssemblyToMaterial', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'material_id'),
			'taskTypeToMaterials' => array(self::HAS_MANY, 'TaskTypeToMaterial', 'material_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Material',
			'unit_price' => 'Unit price',
			'unit' => 'Unit',
			'client_alias' => 'Client alias',
			'client_id' => 'Client',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.client_alias',$this->client_alias,true);
		$criteria->compare('t.unit_price',$this->unit_price);
		$criteria->compare('t.unit',$this->unit);
		$criteria->compare('t.client_id', $this->client_id);

		$criteria->select=array(
			't.id',
			't.description',
			't.client_alias',
			't.unit',
			't.unit_price',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = 'client_alias';
		$columns[] = 'unit_price';
		$columns[] = 'unit';
		
		return $columns;
	}
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'description',
			'client_alias',
		);
	}

	public function scopeClient($assembly_id)
	{
		$criteria=new CDbCriteria;
		$assembly = Assembly::model()->findByPk($assembly_id);
		$criteria->compare('client_id', $assembly->client_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	

}

?>