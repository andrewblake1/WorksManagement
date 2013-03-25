<?php

/**
 * This is the model class for table "generic_task_type".
 *
 * The followings are the available columns in table 'generic_task_type':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $generictaskcategory_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Generictaskcategory $generictaskcategory
 * @property Staff $staff
 * @property TaskType $taskType
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class GenericTaskType extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom type';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTaskType;
	public $searchGenerictaskcategory;
	public $searchGenericType;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, generic_type_id', 'required'),
			array('task_type_id, generic_type_id', 'numerical', 'integerOnly'=>true),
			array('generictaskcategory_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, searchTaskType, searchGenerictaskcategory, searchGenericType', 'safe', 'on'=>'search'),
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
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'generictaskcategory' => array(self::BELONGS_TO, 'Generictaskcategory', 'generictaskcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'generic_task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_type_id' => 'Client/Project type/Task type',
			'searchTaskType' => 'Client/Project type/Task type',
			'generictaskcategory_id' => 'Task category',
			'searchGenerictaskcategory' => 'Task category',
			'generic_type_id' => 'Custom type',
			'searchGenericType' => 'Custom type',
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
			't.generictaskcategory_id',
			't.generic_type_id',
			'generictaskcategory.name AS searchGenerictaskcategory',
			'genericType.description AS searchGenericType',
		);

		// where
		$criteria->compare('generictaskcategory.name',$this->searchGenerictaskcategory,true);
		$criteria->compare('genericType.description',$this->searchGenericType,true);
		$criteria->compare('t.task_type_id',$this->task_type_id);

		// join
		$criteria->with = array(
			'generictaskcategory',
			'genericType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchGenerictaskcategory', 'Generictaskcategory', 'generictaskcategory_id');
        $columns[] = static::linkColumn('searchGenericType', 'GenericType', 'generic_type_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'genericType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchGenerictaskcategory', 'searchGenericType');
	}

}

?>