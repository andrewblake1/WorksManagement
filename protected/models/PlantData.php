<?php

/**
 * This is the model class for table "tbl_plant_data".
 *
 * The followings are the available columns in table 'tbl_plant_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $plant_id
 * @property integer $mode_id
 * @property integer $plant_to_supplier_id
 * @property integer $estimated_total_quantity
 * @property string $estimated_total_duration
 * @property string $start
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Planning $planning
 * @property Planning $level
 * @property User $updatedBy
 * @property Mode $mode
 * @property PlantToSupplier $plant
 * @property PlantToSupplier $plantToSupplier
 * @property ActionToPlant $actionToPlant
 * @property PlantDataToPlantCapability[] $plantDataToPlantCapabilities
 * @property TaskToPlant[] $taskToPlants
 */
class PlantData extends ActiveRecord
{

	public $searchPlant;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
			'level' => array(self::BELONGS_TO, 'Planning', 'level'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
			'plant' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_id'),
			'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_to_supplier_id'),
			'plantDataToPlantCapabilities' => array(self::HAS_MANY, 'PlantDataToPlantCapability', 'plant_data_id'),
			'taskToPlants' => array(self::HAS_MANY, 'TaskToPlant', 'plant_data_id'),
		);
	}


	/**
	 * Need to deal with level modification here as can't do easily within trigger due to trigger
	 * not allowing modification of same table outside the row being modified. Could use blackhole table
	 * with trigger on it to do what we need but can't see advantage over doing it in application here - would
	 * need to alter table name here to black hole table name. Np advantage as if user alters direct in database, would
	 * only work if they used the black hole table
	 * @param type $attributes
	 */
	public function update($attributes = null)
	{
		// if the level has changed
		if($this->attributeChanged('level'))
		{
			$oldLevel = $this->getOldAttributeValue('level');
			$newLevel = $this->level;
			// if the level number is decreasing - heading toward project - converge
			if($newLevel < $oldLevel)
			{
				// ansestor search
				$targetPlanningId = Yii::app()->db->createCommand('
					SELECT id FROM tbl_planning planning
					WHERE planning.level = :newLevel
						AND planning.lft <= (SELECT lft FROM tbl_planning WHERE id = :planningId)
						AND planning.rgt >= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
						AND planning.root = (SELECT root FROM tbl_planning WHERE id = :planningId)
				')->queryScalar(array(':newLevel'=>$newLevel, ':planningId'=>$this->planning_id));
		
				// if a plant_data already exists for this at new target level
				if($mergePlantDataId=Yii::app()->db->createCommand('
					SELECT id FROM tbl_plant_data
					WHERE plant_id = :plantId
						AND planning_id = :targetPlanningId
						AND mode_id = :modeId
					')->queryScalar(array(
						':plantId'=>$this->plant_id,
						':modeId'=>$this->mode_id,
						':targetPlanningId'=>$targetPlanningId
				)))
				{
					// update existing tbl_task_to_plant records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_plant taskToPlant
						SET plant_data_id = :mergePlantDataId
						WHERE plant_data_id = :thisId
					')->execute(array(':mergePlantDataId'=>$mergePlantDataId, ':thisId'=>$this->id));
					
					// don't need to delete as a trigger on update shoujld have done this
					return true;
				}
				// otherwise just shifting this one to the new level
				else
				{
					$this->planning_id = $targetPlanningId;
					return parent::update();
				}
			}
			// otherwise the level number is increasing - heading toward task - diverge
			else
			{
				// insert new suitable plant data records at the desired level of each related item at the desired level
				// and modify existing plant records to point at the new relevant plant_data
				$plantData = new self;
				$plantData->plant_id = $this->plant_id;
				$plantData->level = $newLevel;
				$plantData->mode_id = $this->mode_id;
				$plantData->plant_to_supplier_id = $this->plant_to_supplier_id;
				$plantData->estimated_total_quantity = $this->estimated_total_quantity;
				$plantData->estimated_total_duration = $this->estimated_total_duration;
				$plantData->start = $this->start;
				$plantData->updated_by = Yii::app()->user->id;
				// loop thru all relevant new planning id's
				// child hunt
				$command=Yii::app()->db->createCommand('
					SELECT id, lft, rgt FROM tbl_planning planning
					WHERE planning.level = :newLevel
						AND planning.lft >= (SELECT lft FROM tbl_planning WHERE id = :planningId)
						AND planning.rgt <= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
						AND planning.root = (SELECT root FROM tbl_planning WHERE id = :planningId)
				');
				foreach($command->queryAll(true, array(':newLevel'=>$newLevel, 'planningId'=>$this->planning_id)) as $planning)
				{
					$plantData->planning_id = $planning['id'];
					
					// if a data row does not already exists
					if(!PlantData::model()->findByAttributes(array(
						'planning_id' => $plantData->planning_id,
						'plant_id' => $plantData->plant_id,
						'mode_id' => $plantData->mode_id,
					))) 
					{
						$plantData->insert();
 					
						// make the relevant tbl_task_to_plant items relate i.e. those that are descendants of or equal the planningId
						// e.g. where task's.lft >= planningId.lft AND task's.rgt <= planningId.rgt
						Yii::app()->db->createCommand('
							UPDATE tbl_task_to_plant JOIN tbl_planning AS task ON tbl_task_to_plant.task_id = task.id
							SET plant_data_id = :newPlantDataId
							WHERE plant_data_id = :oldPlantDataId
								AND task.lft >= :planningLft
								AND task.rgt <= :planningRgt
						')->execute(array(
							':newPlantDataId'=>$plantData->id,
							':oldPlantDataId'=>$this->id,
							':planningLft'=>$planning['lft'],
							':planningRgt'=>$planning['rgt'],
						));

						// reset for next iteration
						$plantData->id = NULL;
						$plantData->setIsNewRecord(true);
					}
				}

				// delete of this shouldn't be necassary as update trigger should have taken care of it in child table
				return true;
			}
		}
		else
		{
			return parent::update();
		}
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchPlant', $this->searchPlant, 'plant.auth_item_name', true);

		// with
		$criteria->with = array(
			'plant',
		);

		return $criteria;
	}

	public static function getDisplayAttr()
	{
		// just a dummy
		return array(
			'searchPlant',
		);
	}

	public function scopePlanning($exclude_id, $planning_id, $mode_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('t.planning_id', $planning_id);
		$criteria->compare('t.mode_id', $mode_id);
		$criteria->addNotInCondition('t.id', array($exclude_id));

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}