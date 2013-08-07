<?php

/**
 * This is the model class for table "tbl_task_template_to_plant_capability".
 *
 * The followings are the available columns in table 'tbl_task_template_to_plant_capability':
 * @property integer $id
 * @property integer $task_template_to_plant_id
 * @property integer $plant_capabilty_id
 * @property integer $plant_to_supplier_to_plant_capabilty_id
 * @property integer $plant_to_supplier_id
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskTemplateToPlant $taskTemplateToPlant
 * @property PlantToSupplierToPlantCapabilty $plantToSupplier
 * @property PlantToSupplierToPlantCapabilty $plantToSupplierToPlantCapabilty
 * @property PlantToSupplierToPlantCapabilty $plantCapabilty
 */
class TaskTemplateToPlantCapability extends ActiveRecord
{
	public $searchPlantCapability;
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskTemplateToPlant' => array(self::BELONGS_TO, 'TaskTemplateToPlant', 'task_template_to_plant_id'),
            'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_to_supplier_id'),
            'plantToSupplierToPlantCapabilty' => array(self::BELONGS_TO, 'PlantToSupplierToPlantCapabilty', 'plant_to_supplier_to_plant_capabilty_id'),
            'plantCapabilty' => array(self::BELONGS_TO, 'PlantCapabilty', 'plant_capabilty_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchPlantCapability', $this->searchPlant, 'plantCapabilty.description', true);

		// with
		$criteria->with = array(
			'plantCapabilty',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchPlantCapability';
		
		return $columns;
	}

}