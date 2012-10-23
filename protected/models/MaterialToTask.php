<?php

/**
 * This is the model class for table "material_to_task".
 *
 * The followings are the available columns in table 'material_to_task':
 * @property string $id
 * @property integer $material_id
 * @property string $task_id
 * @property integer $quantity
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Material $material
 * @property Task $task
 * @property Staff $staff
 */
class MaterialToTask extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterial;
	public $searchTask;

	public $store_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'material_to_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, material_id, task_id, quantity, staff_id', 'required'),
			array('store_id, material_id, quantity, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchMaterial, searchTask, quantity, searchStaff', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Material to task',
			'material_id' => 'Material',
			'searchMaterial' => 'Material',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'quantity' => 'Quantity',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.material_id',
			'material.description AS searchMaterial',
			't.quantity',
		);
		
		// where
		$criteria->compare('material.description',$this->searchMaterial,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.task_id',$this->task_id);

		// join
		$criteria->with = array(
			'material',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchMaterial', 'Material', 'material_id');
		$columns[] = 'quantity';
		
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
	
	static function getDisplayAttr()
	{
		return array('material->description');
	}

	public function afterFind() {
		$this->store_id = $this->material->store_id;
		
		return parent::afterFind();
	}
}

?>