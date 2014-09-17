<?php

/**
 * This is the model class for table "tbl_duty_data".
 *
 * The followings are the available columns in table 'tbl_duty_data':
 * @property string $id
 * @property string $planning_id
 * @property integer $duty_step_id
 * @property string $level
 * @property integer $responsible
 * @property string $updated
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property User $updatedBy
 * @property Planning $planning
 * @property Planning $level
 * @property User $responsible0
 * @property DutyStep $dutyStep
 * @property DutyDataToDutyStepToCustomField[] $dutyDataToDutyStepToCustomFields
 */
class DutyData extends ActiveRecord
{
	public $searchDutyStep;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(array('updated')), array(
			array('updated', 'safe'),
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
            'duties' => array(self::HAS_MANY, 'Duty', 'duty_data_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
            'level' => array(self::BELONGS_TO, 'Planning', 'level'),
            'responsible0' => array(self::BELONGS_TO, 'User', 'responsible'),
            'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
            'dutyDataToDutyStepToCustomFields' => array(self::HAS_MANY, 'DutyDataToDutyStepToCustomField', 'duty_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'duty_step_id' => 'Duty',
			'updated' => 'Completed',
		));
	}

	public function beforeSave()
	{
		// if the updated attribute was null but is now being set
		if(!empty($this->updated) && $this->getOldAttributeValue('updated') == null)
		{
			// set to current datetime
			$this->updated = date('Y-m-d H:i:s');
		}
		// system admin clear
		elseif(empty($this->updated) && Yii::app()->user->checkAccess('system admin'))
		{
			// clear
			$this->updated = null;
		}
		
		return parent::beforeSave();
	}

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchDutyStep', $this->searchDutyStep, 'dutyStep.description', true);
		
		$criteria->with = array(
			'dutyStep',
		);

		return $criteria;
	}

	static function getDisplayAttr()
	{
		return array(
			'searchDutyStep',
		);
	}
	
	/**
	 * Need to deal with level modification here as can't do easily within trigger due to trigger
	 * not allowing modification of same table outside the row being modified. Could use blackhole table
	 * with trigger on it to do what we need but can't see advantage over doing it in application here - would
	 * need to alter table name here to black hole table name
	 * @param type $attributes
	 */
// TODO: NB: this is effectively changing the _data level for all connected duties and not just a single duty - hence the operation
// of this may need to be assesed as this may be limiting perhaps?
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
		
				// if a duty_data already exists for this step at new target level
				if($mergeDutyDataId=Yii::app()->db->createCommand('
					SELECT * FROM tbl_duty_data
					WHERE duty_step_id = :dutyStepId
						AND planning_id = :targetPlanningId
					')->queryScalar(array(':dutyStepId'=>$this->duty_step_id, ':targetPlanningId'=>$targetPlanningId)))
				{
					// merge the custom values
					Yii::app()->db->createCommand('
						UPDATE (SELECT * FROM tbl_duty_data_to_duty_step_to_custom_field customExis
							WHERE duty_data_id = :mergeDutyDataId) AS exis
						JOIN (SELECT * FROM tbl_duty_data_to_duty_step_to_custom_field
							WHERE duty_data_id = :thisId) AS merge
						USING(duty_step_to_custom_field_id)
						SET customExis.custom_value = COALESCE(exis.custom_value, merge.custom_value)
					')->execute(array(':mergeDutyDataId'=>$mergeDutyDataId, ':thisId'=>$this->id));
					// update existing duty records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_duty duty
						SET duty_data_id = :mergeDutyDataId
						WHERE duty_data_id = :thisId
					')->execute(array(':mergeDutyDataId'=>$mergeDutyDataId, ':thisId'=>$this->id));
					
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
				// insert new suitable duty data records at the desired level of each related item at the desired level
				// and modify existing duty records to point at the new relevant duty_data
				$dutyData = new self;
				$dutyData->duty_step_id = $this->duty_step_id;
				$dutyData->level = $newLevel;
				$dutyData->responsible = $this->responsible;
				$dutyData->updated = $this->updated;
				$dutyData->updated_by = Yii::app()->user->id;
				// loop thru all relevant new planning id's
				// child hunt
				$command=Yii::app()->db->createCommand('
					SELECT id, lft, rgt FROM tbl_planning planning
					WHERE planning.level = :newLevel
						AND planning.lft >= (SELECT lft FROM tbl_planning WHERE id = :planningId)
						AND planning.rgt <= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
						AND planning.root = (SELECT root FROM tbl_planning WHERE id = :planningId)
				');
				foreach($command->queryColumn(array(':newLevel'=>$newLevel, 'planningId'=>$this->planning_id)) as $planning)
				{
					$dutyData->planning_id = $planning['id'];
					$dutyData->insert();
					
					// NB: this needs to go before the update statement above because once no related tbl_duty items then update trigger
					// removes unattached tbl_duty items which could cascade here
					// create new set of custom fields for each - cloning from the original which will disappear due to update trigger and cascade
					// delete on foreign key to tbl_duty_data
					Yii::app()->db->createCommand('
					INSERT INTO tbl_duty_data_to_duty_step_to_custom_field (
						custom_value,
						duty_step_to_custom_field_id,
						duty_data_id,
						updated_by
						)
						SELECT
							custom_value,
							duty_step_to_custom_field_id,
							:newDutyDataId,
							:updatedBy
						FROM tbl_duty_data_to_duty_step_to_custom_field
						WHERE duty_data_id = :oldDutyDataId
					')->execute(array(
						':newDutyDataId'=>$dutyData->id,
						':updatedBy'=>$this->updated_by,
						':oldDutyDataId'=>$this->id,
					));
					
					// make the relevant duty items relate
					Yii::app()->db->createCommand('
						UPDATE tbl_duty JOIN tbl_planning AS task ON tbl_duty.task_id = task.id
						SET duty_data_id = :newDutyDataId
						WHERE duty_data_id = :oldDutyDataId
							AND task.lft >= :planningLft
							AND task.rgt <= :planningRgt
					')->execute(array(
						':newDutyDataId'=>$dutyData->id,
						':oldDutyDataId'=>$this->id,
						':planningLft'=>$planning['lft'],
						':planningRgt'=>$planning['rgt'],
					));
					
					// reset for next iteration
					$dutyData->id = NULL;
					$dutyData->setIsNewRecord(true);
				}

				// remove this record as all the related duty items should now point at the correct new target
				// this will remove the old custom fields as well by cascade delete
				// NB: don't return the delete as may delete 0 rows due to orphan maintenance in duty update trigger
				$this->delete();
				
				return true;
			}
		}
		else
		{
			return parent::update();
		}
	}
	
}