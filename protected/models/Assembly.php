<?php

/**
 * This is the model class for table "assembly".
 *
 * The followings are the available columns in table 'assembly':
 * @property integer $id
 * @property string $description
 * @property string $unit_price
 * @property integer $client_id
 * @property string $client_alias
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Client $client
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToAssembly[] $taskToAssemblies1
 * @property TaskTypeToAssembly[] $taskTypeToAssemblies
 * @property TaskTypeToAssembly[] $taskTypeToAssemblies1
 */
class Assembly extends ActiveRecord
{
	public $fileName;
	public $file;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly';
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
			array('deleted, client_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description, client_alias', 'length', 'max'=>255),
			array('unit_price', 'length', 'max'=>7),
			array('file', 'FileAjax', 'types'=>'jpg, gif, png', 'allowEmpty' => true),
			array('id, description, unit_price, client_id, client_alias, searchStaff', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'assembly_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'client_id'),
			'taskToAssemblies1' => array(self::HAS_MANY, 'TaskToAssembly', 'assembly_id'),
			'taskTypeToAssemblies' => array(self::HAS_MANY, 'TaskTypeToAssembly', 'client_id'),
			'taskTypeToAssemblies1' => array(self::HAS_MANY, 'TaskTypeToAssembly', 'assembly_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Assembly',
			'unit_price' => 'Unit price',
			'client_id' => 'Client',
			'client_alias' => 'Client Alias',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.unit_price',$this->unit_price,true);
		$criteria->compare('t.client_id', $this->client_id);

		$criteria->select=array(
			't.id',
			't.description',
			't.unit_price',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = 'unit_price';
		
		return $columns;
	}

	public function scopeClient($parentModelName, $id)
	{
		$criteria=new DbCriteria;
		$parentModel = $parentModelName::model()->findByPk($id);
		$criteria->compare('client_id', $parentModel->client_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>