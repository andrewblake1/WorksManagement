<?php

/**
 * This is the model class for table "tbl_role_data".
 *
 * The followings are the available columns in table 'tbl_role_data':
 * @property string $id
 * @property integer $human_resource_id
 * @property string $planning_id
 * @property string $level
 * @property integer $mode_id
 * @property integer $estimated_total_quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ExclusiveRole[] $exclusiveRoles
 * @property ExclusiveRole[] $exclusiveRoles1
 * @property ExclusiveRole[] $exclusiveRoles2
 * @property Planning $planning
 * @property Planning $level0
 * @property User $updatedBy
 * @property Mode $mode
 * @property HumanResource $humanResource
 * @property TaskToRole[] $taskToRoles
 */
class RoleData extends ActiveRecord
{
	public $searchRole;
	
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
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'humanResource' => array(self::BELONGS_TO, 'HumanResource', 'human_resource_id'),
            'taskToRoles' => array(self::HAS_MANY, 'TaskToRole', 'role_data_id'),
        );
    }

// todo: repeated in humanresourcedata and dutydata -- move to trait
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
		
				// if a role_data already exists for this at new target level
				if($exisRoleDataRow=Yii::app()->db->createCommand('
					SELECT * FROM tbl_role_data
					WHERE human_resource_id = :human_resource_id
						AND planning_id = :targetPlanningId
					')->queryRow(true, array(':human_resource_id'=>$this->human_resource_id, ':targetPlanningId'=>$targetPlanningId)))
				{
					$exisRoleDataTarget = new self;
					$exisRoleDataTarget->attributes = $exisRoleDataRow;
// beware - not sure if id is safe?
					$exisRoleDataTarget->setIsNewRecord(false);
					// update existing tbl_task_to_role records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_role taskToRole
						SET role_data_id = :exisRoleDataTargetid
						WHERE role_data_id = :mergeRoleId
					')->execute(array(':exisRoleDataTargetid'=>$exisRoleDataTarget->id, ':mergeRoleId'=>$this->id));
					
					// remove this record as all the related role items should now point at the correct new target
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
				// insert new suitable role data records at the desired level of each related item at the desired level
				// and modify existing role records to point at the new relevant role_data
				$roleData = new self;
				$roleData->human_resource_id = $this->human_resource_id;
				$roleData->level = $newLevel;
				$roleData->estimated_total_quantity = $this->estimated_total_quantity;
				$roleData->mode_id = $this->mode_id;
				$roleData->updated_by = Yii::app()->user->id;
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
					$roleData->planning_id = $planningId;
					$roleData->insert();
					
					// make the relevant tbl_task_to_role items relate
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_role
						SET role_data_id = :newRoleDataId
						WHERE role_data_id = :oldRoleDataId
					')->execute(array(':newRoleDataId'=>$roleData->id, ':oldRoleDataId'=>$this->id));
					
					// reset for next iteration
					$roleData->id = NULL;
					$roleData->setIsNewRecord(true);
				}

				// remove this record as all the related role items should now point at the correct new target
				// NB: don't return the delete as may delete 0 rows due to orphan maintenance in role update trigger
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

		$criteria->compareAs('searchRole', $this->searchRole, 'humanResource.auth_item_name', true);

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
			'searchRole',
		);
	}
 
	public function scopePlanning($exclude_id, $planning_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('t.planning_id', $planning_id);
		$criteria->addNotInCondition('t.id', array($exclude_id));

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
}