<?php

/**
 * This is the model class for table "task_type_to_assembly".
 *
 * The followings are the available columns in table 'task_type_to_assembly':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $assembly_id
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
 * @property Assembly $assembly
 * @property Staff $staff
 */
class TaskTypeToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssembly;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';

	public $store_id;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, task_type_id, assembly_id, quantity', 'required'),
			array('store_id, task_type_id, assembly_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, searchAssembly, quantity, minimum, maximum, quantity_tooltip, select', 'safe', 'on'=>'search'),
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
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
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
			'assembly_id' => 'Assembly',
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
			't.assembly_id',
			'assembly.description AS searchAssembly',
			't.quantity',
			't.minimum',
			't.maximum',
			't.select',
			't.quantity_tooltip',
		);

		// where
		$criteria->compare('assembly.description',$this->searchAssembly);
		$criteria->compare('t.task_type_id',$this->task_type_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.select',$this->select,true);

		// join
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchAssembly', 'Assembly', 'assembly_id');
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
			'assembly->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAssembly');
	}

	public function afterFind() {
		$this->store_id = $this->assembly->store_id;
		
		return parent::afterFind();
	}
	
}