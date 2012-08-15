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

	/**
	 * @var search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MaterialToTask the static model class
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
			array('material_id, task_id, quantity, staff_id', 'required'),
			array('material_id, quantity, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchMaterial, searchTask, quantity, searchStaff', 'safe', 'on'=>'search'),
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
			'id' => 'Material To Task',
			'material_id' => 'Material',
			'searchMaterial' => 'Material',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'quantity' => 'Quantity',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('material.description',$this->searchMaterial,true);
		$criteria->compare('task.description',$this->searchTask,true);
		$criteria->compare('quantity',$this->quantity);
		
		$criteria->with = array('material','task');

		$criteria->select=array(
			'id',
			'material.description AS searchMaterial',
			'task.description AS searchTask',
			'quantity',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchMaterial');
	}
}