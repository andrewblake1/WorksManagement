<?php

/**
 * This is the model class for table "assembly".
 *
 * The followings are the available columns in table 'assembly':
 * @property integer $id
 * @property integer $plan_id
 * @property integer $material_id
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Plan $plan
 * @property Material $material
 * @property Staff $staff
 * @property TaskToAssembly[] $taskToAssemblies
 */
class Assembly extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Assembly the static model class
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
			array('plan_id, material_id, quantity, staff_id', 'required'),
			array('plan_id, material_id, quantity, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, plan_id, material_id, quantity, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'plan' => array(self::BELONGS_TO, 'Plan', 'plan_id'),
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'assembly_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'plan_id' => 'Plan',
			'material_id' => 'Material',
			'quantity' => 'Quantity',
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
		$criteria->compare('plan_id',$this->plan_id);
		$criteria->compare('material_id',$this->material_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}