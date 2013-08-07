<?php

/**
 * This is the model class for table "tbl_plant_data_to_plant_capability".
 *
 * The followings are the available columns in table 'tbl_plant_data_to_plant_capability':
 * @property string $id
 * @property string $plant_data_id
 * @property integer $plant_capabilty_id
 * @property integer $plant_to_supplier_id
 * @property integer $quantity
 * @property integer $updated_by
 * @property string $col
 *
 * The followings are the available model relations:
 * @property PlantToSupplierToPlantCapabilty $plantCapabilty
 * @property PlantToSupplierToPlantCapabilty $plantToSupplier
 * @property PlantData $plantData
 * @property User $updatedBy
 */
class PlantDataToPlantCapability extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_plant_data_to_plant_capability';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('plant_data_id, plant_capabilty_id, updated_by', 'required'),
			array('plant_capabilty_id, plant_to_supplier_id, quantity, updated_by', 'numerical', 'integerOnly'=>true),
			array('plant_data_id', 'length', 'max'=>10),
			array('col', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, plant_data_id, plant_capabilty_id, plant_to_supplier_id, quantity, updated_by, col', 'safe', 'on'=>'search'),
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
			'plantCapabilty' => array(self::BELONGS_TO, 'PlantToSupplierToPlantCapabilty', 'plant_capabilty_id'),
			'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplierToPlantCapabilty', 'plant_to_supplier_id'),
			'plantData' => array(self::BELONGS_TO, 'PlantData', 'plant_data_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'plant_data_id' => 'Plant Data',
			'plant_capabilty_id' => 'Plant Capabilty',
			'plant_to_supplier_id' => 'Plant To Supplier',
			'quantity' => 'Quantity',
			'updated_by' => 'Updated By',
			'col' => 'Col',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('plant_data_id',$this->plant_data_id,true);
		$criteria->compare('plant_capabilty_id',$this->plant_capabilty_id);
		$criteria->compare('plant_to_supplier_id',$this->plant_to_supplier_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('updated_by',$this->updated_by);
		$criteria->compare('col',$this->col,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PlantDataToPlantCapability the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
