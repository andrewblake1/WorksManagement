<?php

/**
 * This is the model class for table "tbl_plant_capability".
 *
 * The followings are the available columns in table 'tbl_plant_capability':
 * @property integer $id
 * @property integer $plant_id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Plant $plant
 * @property User $updatedBy
 * @property PlantToSupplierToPlantCapability[] $plantToSupplierToPlantCapabilties
 */
class PlantCapability extends ActiveRecord
{

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'plant' => array(self::BELONGS_TO, 'Plant', 'plant_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'plantToSupplierToPlantCapabilties' => array(self::HAS_MANY, 'PlantToSupplierToPlantCapability', 'plant_capability_id'),
		);
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		
		return $columns;
	}
	
	public function scopePlant($plantId)
	{
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		
		$criteria->compare('t.plant_id', $plantId);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeSupplier($supplierId)
	{
		if($supplierId !== NULL)
		{
			// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
			$criteria=new DbCriteria;

			$criteria->with = 'plantToSupplierToPlantCapabilties.plantToSupplier';
			$criteria->compare('plantToSupplier.supplier_id', $supplierId);

			$this->getDbCriteria()->mergeWith($criteria);
		}
		
		return $this;
	}

}
