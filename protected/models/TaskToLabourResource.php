<?php

/**
 * This is the model class for table "tbl_task_to_labour_resource".
 *
 * The followings are the available columns in table 'tbl_task_to_labour_resource':
 * @property string $id
 * @property string $task_id
 * @property string $labour_resource_data_id
 * @property string $duration
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property LabourResourceData $labourResourceData
 */
class TaskToLabourResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	public $searchPrimarySecondary;
	public $searchLabourResourceToSupplierId;
	public $searchLabourResource;
	public $searchTaskQuantity;
	public $searchMode;
	public $searchEstimatedTotalDuration;
	public $searchEstimatedTotalQuantity;
	public $searchCalculatedTotalDuration;
	public $searchCalculatedTotalQuantity;

	public $estimated_total_quantity;
	public $estimated_total_duration;
	public $start;
	public $auth_item_name;
	public $labour_resource_to_supplier_id;
	public $action_to_labour_resource_id;
	public $searchLevel;
	public $labour_resource_id;
	public $mode_id;
	public $level;
	
	public $durationTemp;	// used to get around an awkward validation situation where want duration to be required if Primary role but not if Secondary role or type not set
	
	public $type;	// role type ie. Primary role or Secondary role

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('labour_resource_data_id', 'type')), array(
			array('labour_resource_id, durationTemp', 'required'),
			array('level, action_to_labour_resource_id, labour_resource_id, mode_id, labour_resource_to_supplier_id, estimated_total_quantity', 'numerical', 'integerOnly'=>true),
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
            'labourResourceData' => array(self::BELONGS_TO, 'LabourResourceData', 'labour_resource_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'labour_resource_to_supplier_id' => 'Supplier',
			'auth_item_name' => 'LabourResource2',
			'estimated_total_duration' => 'Override level duration',
			'estimated_total_quantity' => 'Override level quantity',
			'searchEstimatedTotalDuration' => 'Override level duration',
			'searchEstimatedTotalQuantity' => 'Override level quantity',
			'searchCalculatedTotalDuration' => 'Level duration',
			'searchCalculatedTotalQuantity' => 'Level quantity',
			'durationTemp' => 'Duration',
			'searchPrimarySecondary' => 'Type',
			'searchLabourResource' => 'Role',
			'labour_resource_id' => 'Role',
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
			'IF(primarySecondary.labour_resource_data_id, "Primary", "Secondary") AS searchPrimarySecondary',
		);
		
		$criteria->distinct = true;
		
		# exlude list = failed branch condition or not yet reached branch condition
		$criteria->condition = ' t.id NOT IN (
			SELECT taskToLabourResource.id
			FROM tbl_task_to_labour_resource taskToLabourResource
			JOIN tbl_labour_resource_data labourResourceData
				ON taskToLabourResource.labour_resource_data_id = labourResourceData.id
				AND taskToLabourResource.task_id = :task_id
			JOIN tbl_action_to_labour_resource actionToLabourResource
				ON labourResourceData.action_to_labour_resource_id = actionToLabourResource.id
			JOIN tbl_action_to_labour_resource_branch actionToLabourResourceBranch
				ON actionToLabourResource.id = actionToLabourResourceBranch.id
			JOIN tbl_duty duty
				ON taskToLabourResource.task_id = duty.task_id
			JOIN tbl_duty_data dutyData
				ON duty.duty_data_id = dutyData.id
			JOIN tbl_duty_data_to_duty_step_to_custom_field dutyDataToDutyStepToCustomField
				ON actionToLabourResourceBranch.duty_step_to_custom_field_id = dutyDataToDutyStepToCustomField.duty_step_to_custom_field_id
				AND dutyData.id = dutyDataToDutyStepToCustomField.duty_data_id
				AND (dutyData.updated IS NULL OR NOT dutyDataToDutyStepToCustomField.custom_value REGEXP actionToLabourResourceBranch.compare)
		) ';
		$criteria->params = array(':task_id' => $this->task_id);

		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);
		$criteria->compareAs('searchPrimarySecondary', $this->searchPrimarySecondary, 'IF(primarySecondary.labour_resource_data_id, "Primary", "Secondary")', true);
		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);
		$criteria->compareAs('start', $this->start, 'labourResourceData.start', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchTaskQuantity', $this->searchTaskQuantity, 'task.quantity');
		$criteria->compareAs('searchEstimatedTotalQuantity', $this->searchEstimatedTotalQuantity, 'labourResourceData.estimated_total_quantity');
		$criteria->compareAs('searchEstimatedTotalDuration', $this->searchEstimatedTotalDuration, 'labourResourceData.estimated_total_duration');
		$criteria->compareAs('searchCalculatedTotalQuantity', $this->searchCalculatedTotalQuantity, '(SELECT MAX(quantity) FROM tbl_task_to_labour_resource WHERE labour_resource_data_id = t.labour_resource_data_id)');
		$criteria->compareAs('searchCalculatedTotalDuration', $this->searchCalculatedTotalDuration, '(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) FROM tbl_task_to_labour_resource WHERE labour_resource_data_id = t.labour_resource_data_id)');

		// limit to matching task mode
		$criteria->join = "
			JOIN tbl_task task ON t.task_id = task.id
			JOIN tbl_labour_resource_data labourResourceData ON t.labour_resource_data_id = labourResourceData.id
			JOIN tbl_labour_resource labourResource ON labourResourceData.labour_resource_id = labourResource.id
			JOIN tbl_level level ON labourResourceData.level = level.id
			LEFT JOIN tbl_mode mode
				ON labourResourceData.mode_id = mode.id
				AND task.mode_id = labourResourceData.mode_id
			LEFT JOIN tbl_labour_resource_to_supplier labourResourceToSupplier
				ON labourResourceData.labour_resource_to_supplier_id = labourResourceToSupplier.id
			LEFT JOIN tbl_supplier supplier ON labourResourceToSupplier.supplier_id = supplier.id
			LEFT JOIN tbl_task_to_labour_resource primarySecondary
				ON t.labour_resource_data_id = primarySecondary.labour_resource_data_id
				AND primarySecondary.duration IS NOT NULL
		";
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchLabourResource';
        $columns[] = 'searchPrimarySecondary';
        $columns[] = static::linkColumn('searchSupplier', 'LabourResourceToSupplier', 'searchLabourResourceToSupplierId');
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
			'searchLabourResource',
			'searchMode',
		);
	}

	public function afterFind() {
		parent::afterFind();

		$this->labour_resource_to_supplier_id = $this->labourResourceData->labour_resource_to_supplier_id;
		$this->estimated_total_quantity = $this->labourResourceData->estimated_total_quantity;
		$this->estimated_total_duration = $this->labourResourceData->estimated_total_duration;
		$this->start = $this->labourResourceData->start;
		$this->durationTemp = $this->duration;
		$this->labour_resource_id = $this->labourResourceData->labour_resource_id;
		$this->level = $this->labourResourceData->level;
		$this->mode_id = $this->labourResourceData->mode_id;
		$this->action_to_labour_resource_id = $this->labourResourceData->action_to_labour_resource_id;
		
	}

// TODO:repeated in duties -- use trait but watch setting of level as different in duties slightly
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array(), $taskTemplateToLabourResource=null)
	{
		// ensure existance of a related LabourResourceData. First get the desired planning id which is the desired ancestor of task
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
			$this->labour_resource_to_supplier_id = null;
			$this->estimated_total_duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// retrieve LabourResourceData - or insert if doesn't exist
		if(!$labourResourceData = LabourResourceData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'labour_resource_id'=>$this->labour_resource_id,
		)))
		{
			$labourResourceData = new LabourResourceData;
			$labourResourceData->planning_id = $planning_id;
			$labourResourceData->labour_resource_id = $this->labour_resource_id;
			$labourResourceData->level = $level;
			$labourResourceData->labour_resource_to_supplier_id = $this->labour_resource_to_supplier_id;
			$labourResourceData->estimated_total_quantity = $this->estimated_total_quantity;
			$labourResourceData->estimated_total_duration = $this->estimated_total_duration;
//			$labourResourceData->action_to_labour_resource_id = $this->action_to_labour_resource_id;
			$labourResourceData->start = $this->start;
			$labourResourceData->mode_id = $this->task->mode_id;
			$labourResourceData->updated_by = Yii::app()->user->id;
			$labourResourceData->insert();
		}

		// link this LabourResource to the LabourResourceData
		$this->labour_resource_data_id = $labourResourceData->id;
		
		// a hack to get around not easily being able to adjust rules
		$this->durationTemp = 0;

		parent::createSave($models);
		
		// clear task to labour resource values to indicated secondary role
		if($this->type == 'Secondary')
		{
			$command = Yii::app()->db->createCommand("
				UPDATE `tbl_task_to_labour_resource`
				SET `duration` = NULL, `start` = NULL
				WHERE `labour_resource_data_id` = :labour_resource_data_id");
			$command->bindParam($command, $temp = $this->labour_resource_data_id);
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
			$this->labour_resource_to_supplier_id = null;
			$this->estimated_total_duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// attempt save of related LabourResourceData
		$this->labourResourceData->estimated_total_quantity = $this->estimated_total_quantity;
		$this->labourResourceData->estimated_total_duration = $this->estimated_total_duration;
//		$this->labourResourceData->action_to_labour_resource_id = $this->action_to_labour_resource_id;
		$this->labourResourceData->labour_resource_to_supplier_id = $this->labour_resource_to_supplier_id;
		$this->labourResourceData->start = $this->start;
		$this->labourResourceData->labour_resource_id = $this->labour_resource_id;
		$this->labourResourceData->level = $this->level;
		if($saved &= $this->labourResourceData->updateSave($models))
		{
			// problem here is that the the ...data may have completely changed as a result of convergence or divergence
			// due to a level change
			unset($this->labour_resource_data_id);

			// a hack to get around not easily being able to adjust rules
			$this->durationTemp = 0;

			if(!($saved = $this->dbCallback('save')))
			{
				// put the model into the models array used for showing all errors
				$models[] = $this;
			}
		}
		
		$return = $saved & parent::updateSave($models);
		
		// clear task to labour resource values to indicated secondary role
		if($this->type == 'Secondary')
		{
			$command = Yii::app()->db->createCommand("
				UPDATE `tbl_task_to_labour_resource`
				SET `duration` = NULL
				WHERE `labour_resource_data_id` = :labour_resource_data_id");
			$command->bindParam(':labour_resource_data_id', $temp = $this->labour_resource_data_id);
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
		return $this->action_to_labour_resource_id ? !$this->actionToLabourResource->mandatory : true;
	}

}

?>