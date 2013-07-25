<?php

/**
 * This is the model class for table "tbl_resource_data".
 *
 * The followings are the available columns in table 'tbl_resource_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $resource_id
 * @property integer $mode_id
 * @property integer $resource_to_supplier_id
 * @property integer $estimated_total_quantity
 * @property string $estimated_total_duration
 * @property string $start
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Planning $planning
 * @property Planning $level0
 * @property User $updatedBy
 * @property ResourceToSupplier $resource
 * @property ResourceToSupplier $resourceToSupplier
 * @property Mode $mode
 * @property TaskToResource[] $taskToResources
 */
class ResourceData extends ActiveRecord
{
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('planning_id, level, resource_id, mode_id', 'required'),
			array('resource_id, mode_id, resource_to_supplier_id', 'numerical', 'integerOnly'=>true),
			array('planning_id, level', 'length', 'max'=>10),
			array('start, estimated_total_duration', 'date', 'format'=>'H:m'),
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
            'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'resource' => array(self::BELONGS_TO, 'Resource', 'resource_id'),
			'resourceToSupplier' => array(self::BELONGS_TO, 'ResourceToSupplier', 'resource_to_supplier_id'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'taskToResources' => array(self::HAS_MANY, 'TaskToResource', 'resource_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'planning_id' => 'Planning',
			'resource_id' => 'Resource',
			'resource_to_supplier_id' => 'Supplier',
		));
	}

	/**
	 * Need to deal with level modification here as can't do easily within trigger due to trigger
	 * not allowing modification of same table outside the row being modified. Could use blackhole table
	 * with trigger on it to do what we need but can't see advantage over doing it in application here - would
	 * need to alter table name here to black hole table name
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
		
				// if a resource_data already exists for this at new target level
				if($exisResourceDataRow=Yii::app()->db->createCommand('
					SELECT * FROM tbl_resource_data
					WHERE resource_id = :resourceId
						AND planning_id = :targetPlanningId
					')->queryRow(true, array(':resourceId'=>$this->resource_id, ':targetPlanningId'=>$targetPlanningId)))
				{
					$exisResourceDataTarget = new self;
					$exisResourceDataTarget->attributes = $exisResourceDataRow;
// beware - not sure if id is safe?
					$exisResourceDataTarget->setIsNewRecord(false);
					// update existing resource records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_resource resource
						SET resource_data_id = :exisResourceDataTargetid
						WHERE resource_data_id = :mergeResourceId
					')->execute(array(':exisResourceDataTargetid'=>$exisResourceDataTarget->id, ':mergeResourceId'=>$this->id));
					
					// remove this record as all the related resource items should now point at the correct new target
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
				// insert new suitable resource data records at the desired level of each related item at the desired level
				// and modify existing resource records to point at the new relevant resource_data
				$resourceData = new self;
				$resourceData->resource_id = $this->resource_id;
				$resourceData->level = $newLevel;
				$resourceData->responsible = $this->responsible;
				$resourceData->updated = $this->updated;
				$resourceData->updated_by = Yii::app()->user->id;
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
					$resourceData->planning_id = $planningId;
					$resourceData->insert();
					
					// make the relevant resource items relate
					Yii::app()->db->createCommand('
						UPDATE tbl_resource
						SET resource_data_id = :newResourceDataId
						WHERE resource_data_id = :oldResourceDataId
					')->execute(array(':newResourceDataId'=>$resourceData->id, ':oldResourceDataId'=>$this->id));
					
					// reset for next iteration
					$resourceData->id = NULL;
					$resourceData->setIsNewRecord(true);
				}

				// remove this record as all the related resource items should now point at the correct new target
				// NB: don't return the delete as may delete 0 rows due to orphan maintenance in resource update trigger
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