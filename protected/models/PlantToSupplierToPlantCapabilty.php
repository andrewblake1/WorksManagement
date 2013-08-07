<?php

/**
 * This is the model class for table "tbl_plant_to_supplier_to_plant_capabilty".
 *
 * The followings are the available columns in table 'tbl_plant_to_supplier_to_plant_capabilty':
 * @property integer $id
 * @property integer $plant_to_supplier_id
 * @property integer $plant_capabilty_id
 * @property string $unit_price
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property PlantDataToPlantCapability[] $plantDataToPlantCapabilities
 * @property PlantDataToPlantCapability[] $plantDataToPlantCapabilities1
 * @property PlantToSupplier $plantToSupplier
 * @property PlantCapabilty $plantCapabilty
 * @property User $updatedBy
 * @property TaskTemplateToPlantCapability[] $taskTemplateToPlantCapabilities
 * @property TaskTemplateToPlantCapability[] $taskTemplateToPlantCapabilities1
 * @property TaskTemplateToPlantCapability[] $taskTemplateToPlantCapabilities2
 */
class PlantToSupplierToPlantCapabilty extends ActiveRecord
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
            'plantDataToPlantCapabilities' => array(self::HAS_MANY, 'PlantDataToPlantCapability', 'plant_capabilty_id'),
            'plantDataToPlantCapabilities1' => array(self::HAS_MANY, 'PlantDataToPlantCapability', 'plant_to_supplier_id'),
            'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_to_supplier_id'),
            'plantCapabilty' => array(self::BELONGS_TO, 'PlantCapabilty', 'plant_capabilty_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskTemplateToPlantCapabilities' => array(self::HAS_MANY, 'TaskTemplateToPlantCapability', 'plant_to_supplier_id'),
            'taskTemplateToPlantCapabilities1' => array(self::HAS_MANY, 'TaskTemplateToPlantCapability', 'plant_to_supplier_to_plant_capabilty_id'),
            'taskTemplateToPlantCapabilities2' => array(self::HAS_MANY, 'TaskTemplateToPlantCapability', 'plant_capabilty_id'),
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
        $columns[] = 'unit_price';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchPlantCapability',
		);
	}

	public function scopePlantToSupplier($plantToSupplierId)
	{
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		$criteria->compare('t.plant_to_supplier_id', plantToSupplierId);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}