<?php

/**
 * This is the model class for table "tbl_resource_data".
 *
 * The followings are the available columns in table 'tbl_resource_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $resource_id
 * @property integer $resource_to_supplier_id
 * @property integer $quantity
 * @property string $duration
 * @property string $start
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Planning $planning
 * @property Planning $level0
 * @property User $updatedBy
 * @property ResourceToSupplier $resource
 * @property ResourceToSupplier $resourceToSupplier
 * @property TaskToResource[] $taskToResources
 */
class ResourceData extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ResourceData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('planning_id, level, resource_id, quantity, duration', 'required'),
			array('resource_id, resource_to_supplier_id, quantity', 'numerical', 'integerOnly'=>true),
			array('planning_id, level', 'length', 'max'=>10),
			array('start, duration', 'date', 'format'=>'H:m'),
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
			// beware of gii problem here
            'resource' => array(self::BELONGS_TO, 'Resource', 'resource_id'),
            'resourceToSupplier' => array(self::BELONGS_TO, 'ResourceToSupplier', 'resource_to_supplier_id'),
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
			'level' => 'Level',
			'resource_id' => 'Resource Type',
			'resource_to_supplier_id' => 'Resource Type To Supplier',
			'duration' => 'Hours',
			'start' => 'Start',
		));
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new DbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('planning_id',$this->planning_id,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('resource_to_supplier_id',$this->resource_to_supplier_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('duration',Yii::app()->format->toMysqlTime($this->duration));
		$criteria->compare('start',Yii::app()->format->toMysqlTime($this->start));
		$criteria->compare('updated_by',$this->updated_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
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
// TODO largey repeated in dutyData suggest trait or parent class
		// if the level has changed
		if($this->attributeChanged('level'))
		{
			$oldLevel = $this->oldAttributeValue;
			$newLevel = $this->level;
			// if the level number is decreasing - heading toward project - converge
			if($newLevel < $oldLevel)
			{
				// ansestor search
				$targetPlanningId = Yii::app()->db->createCommand('
					SELECT id FROM tbl_planning
					WHERE planning.level = :newLevel
						AND planning.lft <= (SELECT lft FROM tbl_planning WHERE id = :planningId)
						AND planning.rgt >= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
				')->queryScalar(array(':newLevel'=>$newLevel, ':planningId'=>$this->planning_id));
		
				// if a resource_data already exists for this step at new target level
				if($exisResourceDataRow=Yii::app()->db->createCommand('
					SELECT * FROM tbl_resource_data
					WHERE resource_id = :resourceId
						AND planning_id = :targetPlanningId
					')->queryRow(array(':resourceId'=>$this->resource_id, ':targetPlanningId'=>$targetPlanningId)))
				{
					$exisResourceDataTarget = new self;
					$exisResourceDataTarget->attributes = $exisResourceDataRow;
// beware - not sure if id is safe?
					$exisResourceDataTarget->setIsNewRecord(false);
					// update existing task_to_resource records to now point at this target
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_resource task_to_resource
						SET resource_data_id = :exisResourceDataTargetid
						WHERE resource_data_id = :mergeResourceId
					')->execute(array(':exisResourceDataTargetid'=>$exisResourceDataTarget->id, ':mergeResourceId'=>$this->id));

					// merge supplier id info
					Yii::app()->db->createCommand('
						UPDATE tbl_resource_data
							SET resource_to_supplier_id COALSECE(resource_to_supplier_id, :mergeResourceToSuppllierId)
						WHERE id = :exisResourceDataTargetid
					')->execute(array(
						':mergeResourceToSuppllierId'=>$this->resource_to_supplier_id,
						':exisResourceDataTargetid'=>$exisResourceDataTarget->id,
						));
					
					// remove this record as all the related task_to_resource items should now point at the correct new target
					$this->delete();
				}
				// otherwise just shifting this one to the new level
				else
				{
					$this->planning_id = $targetPlanningId;
					parent::update();
				}
			}
			// otherwise the level number is increasing - heading toward task - diverge
			else
			{
				// insert new suitable task_to_resource data records at the desired level of each related item at the desired level
				// and modify existing task_to_resource records to point at the new relevant resource_data
				$resourceData = new self;
				$resourceData->resource_id = $this->resource_id;
				$resourceData->level = $newLevel;
				$resourceData->responsible = $this->responsible;
				$resourceData->resource_to_supplier_id = $this->resource_to_supplier_id;
				// loop thru all relevant new planning id's
				// child hunt
				$command=Yii::app()->db->createCommand('
					SELECT id FROM tbl_planning
					WHERE planning.level = :newLevel
						AND planning.lft >= (SELECT lft FROM tbl_planning WHERE id = :planningId)
						AND planning.rgt <= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
				');
				foreach($command->queryColumn(array(':newLevel'=>$newLevel, 'planningId'=>$this->planning_id)) as $planningId)
				{
					$resourceData->planning_id = $planningId;
					$resourceData->insert();
					
					// make the relevant task_to_resource items relate - related at task level
					Yii::app()->db->createCommand('
						UPDATE tbl_task_to_resource task_to_resource JOIN tbl_planning planning ON task_to_resource.task_id = planning.id
						SET task_to_resource.resource_data_id = :newResourceDataId
						WHERE planning.lft >= (SELECT lft FROM tbl_planning WHERE id = :planningId)
							AND planning.rgt <= (SELECT rgt FROM tbl_planning WHERE id = :planningId)
					')->execute(array(':newResourceDataId'=>$resourceData->id, ':planningId'=>$planningId));
					
					// reset for next iteration
					$resourceData->id = NULL;
					$resourceData->setIsNewRecord(true);
				}

				// remove this record as all the related task_to_resource items should now point at the correct new target
				$this->delete();
			}
		}
	}
	
}