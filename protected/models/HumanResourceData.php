<?php

/**
 * This is the model class for table "tbl_human_resource_data".
 *
 * The followings are the available columns in table 'tbl_human_resource_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $human_resource_id
 * @property integer $mode_id
 * @property integer $human_resource_to_supplier_id
 * @property integer $estimated_total_quantity
 * @property string $estimated_total_duration
 * @property string $start
 * @property integer $action_to_human_resource_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ExclusiveRole[] $exclusiveRoles
 * @property ExclusiveRole[] $exclusiveRoles1
 * @property ExclusiveRole[] $exclusiveRoles2
 * @property Planning $planning
 * @property Planning $level0
 * @property User $updatedBy
 * @property HumanResourceToSupplier $humanResource
 * @property HumanResourceToSupplier $humanResourceToSupplier
 * @property Mode $mode
 * @property ActionToHumanResource $actionToHumanResource
 * @property TaskToHumanResource[] $taskToHumanResources
 */
class HumanResourceData extends ActiveRecord
{
	public $searchHumanResource;

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'exclusiveRoles' => array(self::HAS_MANY, 'ExclusiveRole', 'planning_id'),
            'exclusiveRoles1' => array(self::HAS_MANY, 'ExclusiveRole', 'parent_id'),
            'exclusiveRoles2' => array(self::HAS_MANY, 'ExclusiveRole', 'child_id'),
            'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
            'level0' => array(self::BELONGS_TO, 'Planning', 'level'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'humanResource' => array(self::BELONGS_TO, 'HumanResource', 'human_resource_id'),
            'humanResourceToSupplier' => array(self::BELONGS_TO, 'HumanResourceToSupplier', 'human_resource_to_supplier_id'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'actionToHumanResource' => array(self::BELONGS_TO, 'ActionToHumanResource', 'action_to_human_resource_id'),
            'taskToHumanResources' => array(self::HAS_MANY, 'TaskToHumanResource', 'human_resource_data_id'),
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
		
				// if a human_resource_data already exists for this at new target level
				if($exisHumanResourceDataRow=Yii::app()->db->createCommand('
					SELECT * FROM tbl_human_resource_data
					WHERE human_resource_id = :humanResourceId
						AND planning_id = :targetPlanningId
					')->queryRow(true, array(':humanResourceId'=>$this->human_resource_id, ':targetPlanningId'=>$targetPlanningId)))
				{
					$exisHumanResourceDataTarget = new self;
					$exisHumanResourceDataTarget->attributes = $exisHumanResourceDataRow;
// beware - not sure if id is safe?
					$exisHumanResourceDataTarget->setIsNewRecord(false);
					// update existing tbl_task_to_human_resource records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_human_resource taskToHumanResource
						SET human_resource_data_id = :exisHumanResourceDataTargetid
						WHERE human_resource_data_id = :mergeHumanResourceId
					')->execute(array(':exisHumanResourceDataTargetid'=>$exisHumanResourceDataTarget->id, ':mergeHumanResourceId'=>$this->id));
					
					// remove this record as all the related humanResource items should now point at the correct new target
					return $this->delete();
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
				// insert new suitable humanResource data records at the desired level of each related item at the desired level
				// and modify existing humanResource records to point at the new relevant human_resource_data
				$humanResourceData = new self;
				$humanResourceData->human_resource_id = $this->human_resource_id;
				$humanResourceData->level = $newLevel;
				$humanResourceData->mode_id = $this->mode_id;
				$humanResourceData->human_resource_to_supplier_id = $this->human_resource_to_supplier_id;
				$humanResourceData->estimated_total_quantity = $this->estimated_total_quantity;
				$humanResourceData->estimated_total_duration = $this->estimated_total_duration;
				$humanResourceData->start = $this->start;
				$humanResourceData->updated_by = Yii::app()->user->id;
				// loop thru all relevant new planning id's
				// child hunt
				$command=Yii::app()->db->createCommand('
					SELECT id FROM tbl_planning planning
					WHERE planning.level = :newLevel
						AND planning.lft >= (SELECT lft FROM tbl_planning WHERE id = :planningId)
						AND planning.rgt <= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
						AND planning.root = (SELECT root FROM tbl_planning WHERE id = :planningId)
				');
				foreach($command->queryColumn(array(':newLevel'=>$newLevel, 'planningId'=>$this->planning_id)) as $planningId)
				{
					$humanResourceData->planning_id = $planningId;
					$humanResourceData->insert();
					
					// make the relevant tbl_task_to_human_resource items relate
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_human_resource
						SET human_resource_data_id = :newHumanResourceDataId
						WHERE human_resource_data_id = :oldHumanResourceDataId
					')->execute(array(':newHumanResourceDataId'=>$humanResourceData->id, ':oldHumanResourceDataId'=>$this->id));
					
					// reset for next iteration
					$humanResourceData->id = NULL;
					$humanResourceData->setIsNewRecord(true);
				}

				// remove this record as all the related tasktohumanResource items should now point at the correct new target
				// NB: don't return the delete as may delete 0 rows due to orphan maintenance in humanResource update trigger
				$this->delete();
				
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

		$criteria->compareAs('searchHumanResource', $this->searchHumanResource, 'humanResource.auth_item_name', true);

		// with
		$criteria->with = array(
			'humanResource',
		);

		return $criteria;
	}

	public static function getDisplayAttr()
	{
		// just a dummy
		return array(
			'searchHumanResource',
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