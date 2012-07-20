<?php

/**
 * This is the model class for table "generic_task_type".
 *
 * The followings are the available columns in table 'generic_task_type':
 * @property integer $id
 * @property integer $client_to_task_type_client_id
 * @property integer $client_to_task_type_task_type_id
 * @property string $description
 * @property integer $generic_task_category_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ClientToTaskType $clientToTaskTypeClient
 * @property ClientToTaskType $clientToTaskTypeTaskType
 * @property GenericType $genericType
 * @property Generictaskcategory $genericTaskCategory
 * @property Staff $staff
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class GenericTaskType extends ActiveRecord
{
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
			array('client_to_task_type_client_id, client_to_task_type_task_type_id, description, generic_type_id, staff_id', 'required'),
			array('client_to_task_type_client_id, client_to_task_type_task_type_id, generic_task_category_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_to_task_type_client_id, client_to_task_type_task_type_id, description, generic_task_category_id, generic_type_id, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'clientToTaskTypeClient' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_client_id'),
			'clientToTaskTypeTaskType' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_task_type_id'),
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'genericTaskCategory' => array(self::BELONGS_TO, 'Generictaskcategory', 'generic_task_category_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'generic_task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_to_task_type_client_id' => 'Client To Task Type Client',
			'client_to_task_type_task_type_id' => 'Client To Task Type Task Type',
			'description' => 'Description',
			'generic_task_category_id' => 'Generic Task Category',
			'generic_type_id' => 'Generic Type',
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('client_to_task_type_client_id',$this->client_to_task_type_client_id);
		$criteria->compare('client_to_task_type_task_type_id',$this->client_to_task_type_task_type_id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('generic_task_category_id',$this->generic_task_category_id);
		$criteria->compare('generic_type_id',$this->generic_type_id);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}