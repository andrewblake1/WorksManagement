<?php

/**
 * This is the model class for table "generic_task_type".
 *
 * The followings are the available columns in table 'generic_task_type':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $generic_task_category_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Generictaskcategory $genericTaskCategory
 * @property Staff $staff
 * @property TaskType $taskType
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class GenericTaskType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTaskType;
	public $searchGenericTaskCategory;
	public $searchGenericType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GenericTaskType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generic_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, generic_type_id, staff_id', 'required'),
			array('task_type_id, generic_task_category_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchTaskType, searchGenericTaskCategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
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
			'genericTaskCategory' => array(self::BELONGS_TO, 'Generictaskcategory', 'generic_task_category_id'),
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
			'id' => 'Generic Task Type',
			'task_type_id' => 'Task Type (Client/Task type)',
			'searchTaskType' => 'Task Type (Client/Task type)',
			'generic_task_category_id' => 'Generic Task Category',
			'searchGenericTaskCategory' => 'Generic Task Category',
			'generic_type_id' => 'Generic Type',
			'searchGenericType' => 'Generic Type',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$this->compositeCriteria($criteria, array(
			'taskType.client.name',
			'taskType.taskType.description'
		), $this->searchTaskType);
		$criteria->compare('genericTaskCategory.description',$this->searchGenericTaskCategory,true);
		$criteria->compare('genericType.description',$this->searchGenericType,true);
		
		$criteria->with = array(
			'taskType.client',
			'taskType',
			'genericTaskCategory',
			'genericType',
			);

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			"CONCAT_WS('$delimiter',
				client.name,
				taskType.description
				) AS searchTaskType",
			'genericTaskCategory.description AS searchGenericTaskCategory',
			'genericType.description AS searchGenericType',
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'client->name',
			'taskType->description',
			'genericType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTaskType', 'searchGenericTaskCategory', 'searchGenericType');
	}

}