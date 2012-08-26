<?php

/**
 * This is the model class for table "task_type_to_material".
 *
 * The followings are the available columns in table 'task_type_to_material':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $material_id
 * @property integer $quantity
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
	public $searchMaterial;
	public $searchTaskType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskTypeToMaterial the static model class
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
		return 'task_type_to_material';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, material_id, quantity, hours, staff_id', 'required'),
			array('task_type_id, material_id, quantity, hours, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, searchTaskType, searchMaterial, quantity, hours, staff_id', 'safe', 'on'=>'search'),
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
		return array(
			'id' => 'ID',
			'task_type_id' => 'Task Type',
			'material_id' => 'Material',
			'quantity' => 'Quantity',
			'staff_id' => 'Staff',
		);
	}


	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
//			't.id',
			'material.description AS searchMaterial',
			't.quantity',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('material.description',$this->searchMaterial);
		$criteria->compare('t.quantity',$this->quantity);

		if(isset($this->task_type_id))
		{
			$criteria->compare('t.task_type_id',$this->task_type_id);
		}
		else
		{
			// Task type
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				projectType.description,
				taskType.description
				) AS searchTaskType";
			$this->compositeCriteria($criteria, array(
				'client.name',
				'projectType.description',
				'taskType.description'
			), $this->searchTaskType);
		}

		// join
		$criteria->with = array(
			'material',
			'taskType',
			'taskType.projectType',
			'taskType.projectType.client',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
        $columns[] = array(
			'name'=>'searchMaterial',
			'value'=>'CHtml::link($data->searchMaterial,
				Yii::app()->createUrl("Material/update", array("id"=>$data->material_id))
			)',
			'type'=>'raw',
		);
 		if(!isset($this->task_id))
		{
			$columns[] = array(
				'name'=>'searchTaskType',
				'value'=>'CHtml::link($data->searchTaskType,
					Yii::app()->createUrl("TaskType/update", array("id"=>$data->task_type_id))
				)',
				'type'=>'raw',
			);
		}
		$columns[] = 'quantity';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
//			'taskType->client'=>'name',
//			'taskType->projectType'=>'description',
//			'taskType'=>'description',
			'material->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchMaterial', 'searchTaskType');
	}
}