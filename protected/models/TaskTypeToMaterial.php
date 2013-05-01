<?php

/**
 * This is the model class for table "task_type_to_material".
 *
 * The followings are the available columns in table 'task_type_to_material':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $material_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property TaskType $taskType
 * @property Material $material
 * @property Staff $staff
 */
class TaskTypeToMaterial extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialAlias;

	public $store_id;
	public $client_alias;

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
			array('store_id, task_type_id, material_id, quantity', 'required'),
			array('store_id, task_type_id, material_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, client_alias, searchMaterialDescription, searchMaterialUnit, searchMaterialAlias, quantity, minimum, maximum, quantity_tooltip, select', 'safe', 'on'=>'search'),
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
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_type_id' => 'Task Type',
//			'material_id' => 'Material',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
		));
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
			't.material_id',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			'material.alias AS searchMaterialAlias',
			"CONCAT_WS('$delimiter',
				material.alias,
				materialToClient.alias
			) AS searchMaterialAlias",
			't.quantity',
			't.minimum',
			't.maximum',
			't.select',
			't.quantity_tooltip',
		);

		// join
		$criteria->join = '
			LEFT JOIN task_type taskType ON t.task_type_id = taskType.id
			LEFT JOIN material_to_client materialToClient ON t.material_id = materialToClient_material_id
				AND taskType.client_id = materialToClient.client_id
		';
		
		// where
		$criteria->compare('t.task_type_id',$this->task_type_id);
		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$criteria->compare('material.alias',$this->searchMaterialAlias,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.select',$this->select,true);

		// join
		$criteria->with = array(
			'material',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchMaterialDescription';
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
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
			'taskType->description',
			'material->description',
			'material->unit',
			'material->alias',
		);
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

	public function afterFind() {
		$this->store_id = $this->material->store_id;
		if($materialToClient = MaterialToClient::model()->findByAttributes(array(
			'material_id'=>$this->material_id,
			'client_id'=>$this->taskType->client,
		)))
		{
			$this->client_alias = $materialToClient->alias;
		}
		
		return parent::afterFind();
	}
	
}