<?php

/**
 * This is the model class for table "tbl_plant_data_to_plant_capability".
 *
 * The followings are the available columns in table 'tbl_plant_data_to_plant_capability':
 * @property string $id
 * @property string $plant_data_id
 * @property integer $plant_capability_id
 * @property integer $plant_to_supplier_id
 * @property integer $quantity
 * @property integer $updated_by
 * @property string $col
 *
 * The followings are the available model relations:
 * @property PlantToSupplierToPlantCapability $plantCapability
 * @property PlantToSupplierToPlantCapability $plantToSupplier
 * @property PlantData $plantData
 * @property User $updatedBy
 */
class PlantDataToPlantCapability extends ActiveRecord
{
	public $task_to_plant_id;
	public $searchPlantCapability;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(), array(
			array('task_to_plant_id', 'numerical', 'integerOnly'=>true),
			array('task_to_plant_id', 'safe'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'plantCapability' => array(self::BELONGS_TO, 'PlantCapability', 'plant_capability_id'),
			'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_to_supplier_id'),
			'plantData' => array(self::BELONGS_TO, 'PlantData', 'plant_data_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		// a slight difference here due to the schema where parent isn't actually task_to_plant
		$taskToPlant = TaskToPlant::model()->findByPk($this->task_to_plant_id);
		$this->plant_data_id = $taskToPlant->plant_data_id;
		
		$criteria->compareAs('searchPlantCapability', $this->searchPlantCapability, 'plantCapability.description', true);

		$criteria->with = array(
			'plantCapability',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchPlantCapability';
        $columns[] = 'quantity';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchPlantCapability',
		);
	}
 
	public function beforeValidate()
	{
		// a slight difference here due to the schema where parent isn't actually task_to_plant
		$taskToPlant = TaskToPlant::model()->findByPk($this->task_to_plant_id);
		$this->plant_data_id = $taskToPlant->plant_data_id;
//		$this->planning_id = $this->parent->planning_id;
		return parent::beforeValidate();
	}
	
	public function afterFind()
	{
		if(isset($_GET['task_to_plant_id']))
		{
			$this->task_to_plant_id = $_GET['task_to_plant_id'];
		}

		return parent::afterFind();
	}
}