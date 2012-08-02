<?php

/**
 * This is the model class for table "generic_task_type".
 *
 * The followings are the available columns in table 'generic_task_type':
 * @property integer $id
 * @property integer $client_to_task_type_id
 * @property string $description
 * @property integer $generic_task_category_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Generictaskcategory $genericTaskCategory
 * @property Staff $staff
 * @property ClientToTaskType $clientToTaskType
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class GenericTaskType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchClientToTaskType;
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
			array('client_to_task_type_id, description, generic_type_id, staff_id', 'required'),
			array('client_to_task_type_id, generic_task_category_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchClientToTaskType, description, searchGenericTaskCategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
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
			'clientToTaskType' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_id'),
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
			'client_to_task_type_id' => 'Client To Task Type (Client/Task type)',
			'searchClientToTaskType' => 'Client To Task Type (Client/Task type)',
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
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$this->compositeCriteria($criteria, array('clientToTaskType.client.name','clientToTaskType.taskType.description'), $this->searchClientToTaskType);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('genericTaskCategory.description',$this->searchGenericTaskCategory);
		$criteria->compare('genericType.description',$this->searchGenericType);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";
		
		$criteria->with = array(
			'staff',
			'clientToTaskType.client',
			'clientToTaskType.taskType',
			'genericTaskCategory',
			'genericType',
			);

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			"CONCAT_WS('$delimiter',
				clientToTaskType.client.name,
				clientToTaskType.taskType.description
				) AS searchClientToTaskType",
			'description',
			'genericTaskCategory.description AS searchGenericTaskCategory',
			'genericType.description AS searchGenericType',
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchClientToTaskType', 'searchGenericTaskCategory', 'searchGenericType');
	}

}