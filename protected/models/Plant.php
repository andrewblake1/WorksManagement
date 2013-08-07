<?php

/**
 * This is the model class for table "tbl_plant".
 *
 * The followings are the available columns in table 'tbl_plant':
 * @property integer $id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ActionToPlant[] $actionToPlants
 * @property User $updatedBy
 * @property PlantCapabilty[] $plantCapabilties
 * @property PlantToSupplier[] $plantToSuppliers
 */
class Plant extends ActiveRecord
{

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'actionToPlants' => array(self::HAS_MANY, 'ActionToPlant', 'plant_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'plantCapabilties' => array(self::HAS_MANY, 'PlantCapabilty', 'plant_id'),
			'plantToSuppliers' => array(self::HAS_MANY, 'PlantToSupplier', 'plant_id'),
		);
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		
		return $columns;
	}

}
