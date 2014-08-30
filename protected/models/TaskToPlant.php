<?php

/**
 * This is the model class for table "tbl_task_to_plant".
 *
 * The followings are the available columns in table 'tbl_task_to_plant':
 * @property string $id
 * @property string $task_id
 * @property string $plant_data_id
 * @property string $duration
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property PlantData $plantData
 */
class TaskToPlant extends ActiveRecord
{
	static $niceNamePlural = 'Plant';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	public $searchPrimarySecondary;
	public $searchPlantToSupplierId;
	public $searchPlant;
	public $searchTaskQuantity;
	public $searchMode;
	public $searchEstimatedTotalDuration;
	public $searchEstimatedTotalQuantity;
	public $searchCalculatedTotalDuration;
	public $searchCalculatedTotalQuantity;

	public $estimated_total_quantity;
	public $estimated_total_duration;
	public $start;
	public $description;
	public $plant_to_supplier_id;
	public $action_to_plant_id;
	public $searchLevel;
	public $plant_id;
	public $mode_id;
	public $level;
	
	public $durationTemp;	// used to get around an awkward validation situation where want duration to be required if Primary role but not if Secondary role or type not set
	
	public $type;	// role type ie. Primary role or Secondary role

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(array('plant_data_id', 'type')), array(
			array('plant_id, durationTemp', 'required'),
			array('level, action_to_plant_id, plant_id, mode_id, plant_to_supplier_id, estimated_total_quantity', 'numerical', 'integerOnly'=>true),
			array('start, duration, estimated_total_duration', 'date', 'format'=>'H:m'),
			array('type', 'safe'),
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
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'plantData' => array(self::BELONGS_TO, 'PlantData', 'plant_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'plant_to_supplier_id' => 'Supplier',
			'estimated_total_duration' => 'Override level duration',
			'estimated_total_quantity' => 'Override level quantity',
			'searchEstimatedTotalDuration' => 'Override level duration',
			'searchEstimatedTotalQuantity' => 'Override level quantity',
			'searchCalculatedTotalDuration' => 'Level duration',
			'searchCalculatedTotalQuantity' => 'Level quantity',
			'durationTemp' => 'Duration',
			'searchPrimarySecondary' => 'Type',
			'searchPlant' => 'Role',
			'plant_id' => 'Role',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);
		
		$criteria->select=array(
			't.*',
			'IF(primarySecondary.plant_data_id, "Primary", "Secondary") AS searchPrimarySecondary',
		);
		
		$criteria->distinct = true;

		# exlude list = failed branch condition or not yet reached branch condition
		$criteria->condition .= ' AND t.id NOT IN (
			SELECT taskToPlant.id
			FROM tbl_task_to_plant taskToPlant
			JOIN tbl_plant_data plantData
				ON taskToPlant.plant_data_id = plantData.id
				AND taskToPlant.task_id = :task_id
			JOIN tbl_action_to_plant actionToPlant
				ON plantData.action_to_plant_id = actionToPlant.id
			JOIN tbl_action_to_plant_branch actionToPlantBranch
				ON actionToPlant.id = actionToPlantBranch.id
			JOIN tbl_duty duty
				ON taskToPlant.task_id = duty.task_id
			JOIN tbl_duty_data dutyData
				ON duty.duty_data_id = dutyData.id
			JOIN tbl_duty_data_to_duty_step_to_custom_field dutyDataToDutyStepToCustomField
				ON actionToPlantBranch.duty_step_to_custom_field_id = dutyDataToDutyStepToCustomField.duty_step_to_custom_field_id
				AND dutyData.id = dutyDataToDutyStepToCustomField.duty_data_id
				AND (dutyData.updated IS NULL OR NOT dutyDataToDutyStepToCustomField.custom_value REGEXP actionToPlantBranch.compare)
		) ';
		$criteria->params[':task_id'] = $this->task_id;

		$criteria->compareAs('searchPlant', $this->searchPlant, 'plant.description', true);
		$criteria->compareAs('searchPrimarySecondary', $this->searchPrimarySecondary, 'IF(primarySecondary.plant_data_id, "Primary", "Secondary")', true);
		$criteria->compareAs('searchPlant', $this->searchPlant, 'plant.description', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);
		$criteria->compareAs('start', $this->start, 'plantData.start', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchTaskQuantity', $this->searchTaskQuantity, 'task.quantity');
		$criteria->compareAs('searchEstimatedTotalQuantity', $this->searchEstimatedTotalQuantity, 'plantData.estimated_total_quantity');
		$criteria->compareAs('searchEstimatedTotalDuration', $this->searchEstimatedTotalDuration, 'plantData.estimated_total_duration');
		$criteria->compareAs('searchCalculatedTotalQuantity', $this->searchCalculatedTotalQuantity, '(SELECT MAX(quantity) FROM tbl_task_to_plant WHERE plant_data_id = t.plant_data_id)');
		$criteria->compareAs('searchCalculatedTotalDuration', $this->searchCalculatedTotalDuration, '(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) FROM tbl_task_to_plant WHERE plant_data_id = t.plant_data_id)');

		// limit to matching task mode
		$criteria->join = "
			JOIN tbl_task task ON t.task_id = task.id
			JOIN tbl_plant_data plantData ON t.plant_data_id = plantData.id
			JOIN tbl_plant plant ON plantData.plant_id = plant.id
			JOIN tbl_level level ON plantData.level = level.id
			JOIN tbl_mode mode
				ON plantData.mode_id = mode.id
				AND task.mode_id = plantData.mode_id
			LEFT JOIN tbl_plant_to_supplier plantToSupplier
				ON plantData.plant_to_supplier_id = plantToSupplier.id
			LEFT JOIN tbl_supplier supplier ON plantToSupplier.supplier_id = supplier.id
			LEFT JOIN tbl_task_to_plant primarySecondary
				ON t.plant_data_id = primarySecondary.plant_data_id
				AND primarySecondary.duration IS NOT NULL
		";
		
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchPlant';
        $columns[] = 'searchPrimarySecondary';
        $columns[] = static::linkColumn('searchSupplier', 'PlantToSupplier', 'searchPlantToSupplierId');
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'start:time';
		$columns[] = 'searchLevel';
//		$columns[] = 'searchMode';
		$columns[] = 'quantity';
		$columns[] = 'duration:time';
		$columns[] = 'searchEstimatedTotalQuantity';
		$columns[] = 'searchEstimatedTotalDuration:time';
		$columns[] = 'searchCalculatedTotalQuantity';
		$columns[] = 'searchCalculatedTotalDuration:time';
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'searchPlant',
			'searchMode',
		);
	}

	public function afterFind() {
		parent::afterFind();

		$this->plant_to_supplier_id = $this->plantData->plant_to_supplier_id;
		$this->estimated_total_quantity = $this->plantData->estimated_total_quantity;
		$this->estimated_total_duration = $this->plantData->estimated_total_duration;
		$this->start = $this->plantData->start;
		$this->durationTemp = $this->duration;
		$this->plant_id = $this->plantData->plant_id;
		$this->level = $this->plantData->level;
		$this->mode_id = $this->plantData->mode_id;
		$this->action_to_plant_id = $this->plantData->action_to_plant_id;
		
	}

// TODO:repeated in duties -- use trait but watch setting of level as different in duties slightly
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array(), $taskTemplateToPlant=null)
	{
		// ensure existance of a related PlantData. First get the desired planning id which is the desired ancestor of task
		// if this is task level

		if(($level = $this->level) == Planning::planningLevelTaskInt)
		{
			$planning_id = $this->task_id;
		}
		else
		{
			// get the desired ansestor
			$planning = Planning::model()->findByPk($this->task_id);

			while($planning = $planning->parent)
			{
				if($planning->level == $this->level)
				{
					break;
				}
			}
			if(empty($planning))
			{
				throw new Exception();
			}

			$planning_id = $planning->id;
		}
		
		// if primary role then all values can be inserted, if secondary then we want to clear
		// start, duration, estimated duration, and supplier for data item and children
		if($this->type == 'Secondary')
		{
			$this->duration = null;
			$this->start = null;
			$this->estimated_total_duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// retrieve PlantData - or insert if doesn't exist
		if(!$plantData = PlantData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'plant_id'=>$this->plant_id,
			'mode_id'=>$this->mode_id,
		)))
		{
			$plantData = new PlantData;
			$plantData->planning_id = $planning_id;
			$plantData->plant_id = $this->plant_id;
			$plantData->level = $level;
			$plantData->plant_to_supplier_id = $this->plant_to_supplier_id;
			$plantData->estimated_total_quantity = $this->estimated_total_quantity;
			$plantData->estimated_total_duration = $this->estimated_total_duration;
//			$plantData->action_to_plant_id = $this->action_to_plant_id;
			$plantData->start = $this->start;
			$plantData->mode_id = $this->mode_id ? $this->mode_id : $this->task->mode_id;
			$plantData->updated_by = Yii::app()->user->id;
			$plantData->insert();
		}

		// link this Plant to the PlantData
		$this->plant_data_id = $plantData->id;
		
		// a hack to get around not easily being able to adjust rules
		$this->durationTemp = 0;

		parent::createSave($models);
		
		// clear task to plant values to indicated secondary role
		if($this->type == 'Secondary')
		{
			$command = Yii::app()->db->createCommand("
				UPDATE `tbl_task_to_plant`
				SET `duration` = NULL, `start` = NULL
				WHERE `plant_data_id` = :plant_data_id");
			$command->bindParam(':plant_data_id', $temp = $this->plant_data_id);
			$command->execute();
		}

		// not interested in failed duplicates
		return true;
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{	
		$saved = true;

		// if primary role then all values can be inserted, if secondary then we want to clear
		// start, duration, estimated duration, and supplier for data item and children
		if($this->type == 'Secondary')
		{
			$this->duration = null;
			$this->start = null;
			$this->plant_to_supplier_id = null;
			$this->estimated_total_duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// attempt save of related PlantData
		$this->plantData->estimated_total_quantity = $this->estimated_total_quantity;
		$this->plantData->estimated_total_duration = $this->estimated_total_duration;
//		$this->plantData->action_to_plant_id = $this->action_to_plant_id;
		$this->plantData->plant_to_supplier_id = $this->plant_to_supplier_id;
		$this->plantData->start = $this->start;
		$this->plantData->plant_id = $this->plant_id;
		$this->plantData->level = $this->level;
		if($saved &= $this->plantData->updateSave($models))
		{
			// problem here is that the the ...data may have completely changed as a result of convergence or divergence
			// due to a level change
			unset($this->plant_data_id);

			// a hack to get around not easily being able to adjust rules
			$this->durationTemp = 0;

			if(!($saved = $this->dbCallback('save')))
			{
				// put the model into the models array used for showing all errors
				$models[] = $this;
			}
		}
		
		$return = $saved & parent::updateSave($models);
		
		// clear task to plant values to indicated secondary role
		if($this->type == 'Secondary')
		{
			$command = Yii::app()->db->createCommand("
				UPDATE `tbl_task_to_plant`
				SET `duration` = NULL
				WHERE `plant_data_id` = :plant_data_id");
			$command->bindParam(':plant_data_id', $temp = $this->plant_data_id);
			$command->execute();
		}

		return $return;
	}
	
	public function beforeValidate()
	{
		// if secondary role then duration can be null so just set as 0
		if($this->type == 'Secondary')
		{
			$this->duration = NULL;
			$this->durationTemp = 1;
		}

		return parent::beforeValidate();
	}
	
	// see if this is deleteable in the application - not blocked by trigger as could interfere with all removals ultimately
	public function getCanDelete()
	{
		return $this->action_to_plant_id ? !$this->actionToPlant->mandatory : true;
	}

}

?>