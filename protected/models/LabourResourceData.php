<?php

/**
 * This is the model class for table "tbl_labour_resource_data".
 *
 * The followings are the available columns in table 'tbl_labour_resource_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $labour_resource_id
 * @property integer $mode_id
 * @property integer $labour_resource_to_supplier_id
 * @property integer $estimated_total_quantity
 * @property string $estimated_total_duration
 * @property string $start
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property MutuallyExclusiveRole[] $mutuallyExclusiveRoles
 * @property MutuallyExclusiveRole[] $mutuallyExclusiveRoles1
 * @property Planning $planning
 * @property Planning $level
 * @property LabourResourceToSupplier $labourResource
 * @property LabourResourceToSupplier $labourResourceToSupplier
 * @property User $updatedBy
 * @property ActionToLabourResource $actionToLabourResource
 * @property Mode $mode
 * @property TaskToLabourResource[] $taskToLabourResources
 */
class LabourResourceData extends ActiveRecord
{
	public $searchLabourResource;

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mutuallyExclusiveRoles' => array(self::HAS_MANY, 'MutuallyExclusiveRole', 'planning_id'),
            'mutuallyExclusiveRoles1' => array(self::HAS_MANY, 'MutuallyExclusiveRole', 'parent_id'),
            'mutuallyExclusiveRoles2' => array(self::HAS_MANY, 'MutuallyExclusiveRole', 'child_id'),
            'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
            'level' => array(self::BELONGS_TO, 'Level', 'level'),
            'labourResource' => array(self::BELONGS_TO, 'LabourResource', 'labour_resource_id'),
            'labourResourceToSupplier' => array(self::BELONGS_TO, 'LabourResourceToSupplier', 'labour_resource_to_supplier_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'taskToLabourResources' => array(self::HAS_MANY, 'TaskToLabourResource', 'labour_resource_data_id'),
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
		
				// if a labour_resource_data already exists for this at new target level
				if($mergeLabourResourceDataId=Yii::app()->db->createCommand('
					SELECT id FROM tbl_labour_resource_data
					WHERE labour_resource_id = :labourResourceId
						AND planning_id = :targetPlanningId
						AND mode_id = :modeId
					')->queryScalar(array(
						':labourResourceId'=>$this->labour_resource_id,
						':modeId'=>$this->mode_id,
						':targetPlanningId'=>$targetPlanningId
				)))
				{
					// update existing tbl_task_to_labour_resource records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_labour_resource taskToLabourResource
						SET labour_resource_data_id = :mergeLabourResourceDataId
						WHERE labour_resource_data_id = :thisId
					')->execute(array(':mergeLabourResourceDataId'=>$mergeLabourResourceDataId, ':thisId'=>$this->id));
					
					// don't need to delete as a trigger on update will have done this
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
				// insert new suitable labourResource data records at the desired level of each related item at the desired level
				// and modify existing labourResource records to point at the new relevant labour_resource_data
				$labourResourceData = new self;
				$labourResourceData->labour_resource_id = $this->labour_resource_id;
				$labourResourceData->level = $newLevel;
				$labourResourceData->mode_id = $this->mode_id;
				$labourResourceData->labour_resource_to_supplier_id = $this->labour_resource_to_supplier_id;
				$labourResourceData->estimated_total_quantity = $this->estimated_total_quantity;
				$labourResourceData->estimated_total_duration = $this->estimated_total_duration;
				$labourResourceData->start = $this->start;
				$labourResourceData->updated_by = Yii::app()->user->id;
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
					$labourResourceData->planning_id = $planning['id'];
 					$labourResourceData->insert();
					
					// make the relevant tbl_task_to_labour_resource items relate i.e. those that are descendants of or equal the planningId
					// e.g. where task's.lft >= planningId.lft AND task's.rgt <= planningId.rgt
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_labour_resource JOIN tbl_planning AS task ON tbl_task_to_labour_resource.task_id = task.id
						SET labour_resource_data_id = :newLabourResourceDataId
						WHERE labour_resource_data_id = :oldLabourResourceDataId
							AND task.lft >= :planningLft
							AND task.rgt <= :planningRgt
					')->execute(array(
						':newLabourResourceDataId'=>$labourResourceData->id,
						':oldLabourResourceDataId'=>$this->id,
						':planningLft'=>$planning['lft'],
						':planningRgt'=>$planning['rgt'],
					));
					
					// reset for next iteration
					$labourResourceData->id = NULL;
					$labourResourceData->setIsNewRecord(true);
				}

				// delete of this planning id shouldn't be necassary as update trigger should have taken care of it in child table
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

		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);

		// with
		$criteria->with = array(
			'labourResource',
		);

		return $criteria;
	}

	public static function getDisplayAttr()
	{
		// just a dummy
		return array(
			'searchLabourResource',
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