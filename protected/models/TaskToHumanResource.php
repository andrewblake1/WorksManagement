<?php

/**
 * This is the model class for table "tbl_task_to_human_resource".
 *
 * The followings are the available columns in table 'tbl_task_to_human_resource':
 * @property string $id
 * @property string $task_id
 * @property string $human_resource_data_id
 * @property string $duration
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property HumanResourceData $humanResourceData
 */
class TaskToHumanResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	public $searchPrimarySecondary;
	public $searchHumanResourceToSupplierId;
	public $searchHumanResource;
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
	public $human_resource_to_supplier_id;
	public $action_to_human_resource_id;
	public $searchLevel;
	public $human_resource_id;
	public $mode_id;
	public $level;
	
	public $durationTemp;	// used to get around an awkward validation situation where want duration to be required if Primary role but not if Secondary role or type not set
	
	public $type;	// role type ie. Primary role or Secondary role

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('human_resource_data_id', 'type')), array(
			array('human_resource_id, durationTemp', 'required'),
			array('level, action_to_human_resource_id, human_resource_id, mode_id, human_resource_to_supplier_id, estimated_total_quantity', 'numerical', 'integerOnly'=>true),
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
            'humanResourceData' => array(self::BELONGS_TO, 'HumanResourceData', 'human_resource_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'human_resource_to_supplier_id' => 'Supplier',
			'auth_item_name' => 'HumanResource2',
			'estimated_total_duration' => 'Override level duration',
			'estimated_total_quantity' => 'Override level quantity',
			'searchEstimatedTotalDuration' => 'Override level duration',
			'searchEstimatedTotalQuantity' => 'Override level quantity',
			'searchCalculatedTotalDuration' => 'Level duration',
			'searchCalculatedTotalQuantity' => 'Level quantity',
			'durationTemp' => 'Duration',
			'searchPrimarySecondary' => 'Type',
			'searchHumanResource' => 'Role',
			'human_resource_id' => 'Role',
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
			'IF(primarySecondary.human_resource_data_id, "Primary", "Secondary") AS searchPrimarySecondary',
		);
		
		$criteria->distinct = true;

		$criteria->compareAs('searchHumanResource', $this->searchHumanResource, 'humanResource.auth_item_name', true);
		$criteria->compareAs('searchPrimarySecondary', $this->searchPrimarySecondary, 'IF(primarySecondary.human_resource_data_id, "Primary", "Secondary")', true);
		$criteria->compareAs('searchHumanResource', $this->searchHumanResource, 'humanResource.auth_item_name', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);
		$criteria->compareAs('start', $this->start, 'humanResourceData.start', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchTaskQuantity', $this->searchTaskQuantity, 'task.quantity');
		$criteria->compareAs('searchEstimatedTotalQuantity', $this->searchEstimatedTotalQuantity, 'humanResourceData.estimated_total_quantity');
		$criteria->compareAs('searchEstimatedTotalDuration', $this->searchEstimatedTotalDuration, 'humanResourceData.estimated_total_duration');
		$criteria->compareAs('searchCalculatedTotalQuantity', $this->searchCalculatedTotalQuantity, '(SELECT MAX(quantity) FROM tbl_task_to_human_resource WHERE human_resource_data_id = t.human_resource_data_id)');
		$criteria->compareAs('searchCalculatedTotalDuration', $this->searchCalculatedTotalDuration, '(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) FROM tbl_task_to_human_resource WHERE human_resource_data_id = t.human_resource_data_id)');

		// limit to matching task mode
		$criteria->join = "
			JOIN tbl_task task ON t.task_id = task.id
			JOIN tbl_human_resource_data humanResourceData ON t.human_resource_data_id = humanResourceData.id
			JOIN tbl_human_resource humanResource ON humanResourceData.human_resource_id = humanResource.id
			JOIN tbl_level level0 ON humanResourceData.level = level0.id
			LEFT JOIN tbl_mode mode
				ON humanResourceData.mode_id = mode.id
				AND task.mode_id = humanResourceData.mode_id
			LEFT JOIN tbl_human_resource_to_supplier humanResourceToSupplier
				ON humanResourceData.human_resource_to_supplier_id = humanResourceToSupplier.id
			LEFT JOIN tbl_supplier supplier ON humanResourceToSupplier.supplier_id = supplier.id
			LEFT JOIN tbl_task_to_human_resource primarySecondary
				ON t.human_resource_data_id = primarySecondary.human_resource_data_id
				AND primarySecondary.duration IS NOT NULL
		";
		
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchHumanResource';
        $columns[] = 'searchPrimarySecondary';
        $columns[] = static::linkColumn('searchSupplier', 'HumanResourceToSupplier', 'searchHumanResourceToSupplierId');
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
			'searchHumanResource',
			'searchMode',
		);
	}

	public function afterFind() {
		parent::afterFind();

		$this->human_resource_to_supplier_id = $this->humanResourceData->human_resource_to_supplier_id;
		$this->estimated_total_quantity = $this->humanResourceData->estimated_total_quantity;
		$this->estimated_total_duration = $this->humanResourceData->estimated_total_duration;
		$this->start = $this->humanResourceData->start;
		$this->durationTemp = $this->duration;
		$this->human_resource_id = $this->humanResourceData->human_resource_id;
		$this->level = $this->humanResourceData->level;
		$this->mode_id = $this->humanResourceData->mode_id;
		$this->action_to_human_resource_id = $this->humanResourceData->action_to_human_resource_id;
		
	}

// TODO:repeated in duties -- use trait but watch setting of level as different in duties slightly
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array(), $taskTemplateToHumanResource=null)
	{
		// ensure existance of a related HumanResourceData. First get the desired planning id which is the desired ancestor of task
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
			$this->human_resource_to_supplier_id = null;
			$this->estimated_total_duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// retrieve HumanResourceData - or insert if doesn't exist
		if(!$humanResourceData = HumanResourceData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'human_resource_id'=>$this->human_resource_id,
		)))
		{
			$humanResourceData = new HumanResourceData;
			$humanResourceData->planning_id = $planning_id;
			$humanResourceData->human_resource_id = $this->human_resource_id;
			$humanResourceData->level = $level;
			$humanResourceData->human_resource_to_supplier_id = $this->human_resource_to_supplier_id;
			$humanResourceData->estimated_total_quantity = $this->estimated_total_quantity;
			$humanResourceData->estimated_total_duration = $this->estimated_total_duration;
//			$humanResourceData->action_to_human_resource_id = $this->action_to_human_resource_id;
			$humanResourceData->start = $this->start;
			$humanResourceData->mode_id = $this->task->mode_id;
			$humanResourceData->updated_by = Yii::app()->user->id;
			$humanResourceData->insert();
		}

		// link this HumanResource to the HumanResourceData
		$this->human_resource_data_id = $humanResourceData->id;
		
		// a hack to get around not easily being able to adjust rules
		$this->durationTemp = 0;

		parent::createSave($models);
		
		// clear task to human resource values to indicated secondary role
		if($this->type == 'Secondary')
		{
			$command = Yii::app()->db->createCommand("
				UPDATE `tbl_task_to_human_resource`
				SET `duration` = NULL, `start` = NULL
				WHERE `human_resource_data_id` = :human_resource_data_id");
			$command->bindParam($command, $temp = $this->human_resource_data_id);
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
			$this->human_resource_to_supplier_id = null;
			$this->estimated_total_duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// attempt save of related HumanResourceData
		$this->humanResourceData->estimated_total_quantity = $this->estimated_total_quantity;
		$this->humanResourceData->estimated_total_duration = $this->estimated_total_duration;
//		$this->humanResourceData->action_to_human_resource_id = $this->action_to_human_resource_id;
		$this->humanResourceData->human_resource_to_supplier_id = $this->human_resource_to_supplier_id;
		$this->humanResourceData->start = $this->start;
		$this->humanResourceData->human_resource_id = $this->human_resource_id;
		$this->humanResourceData->level = $this->level;
		if($saved &= $this->humanResourceData->updateSave($models))
		{
			// problem here is that the the ...data may have completely changed as a result of convergence or divergence
			// due to a level change
			unset($this->human_resource_data_id);

			// a hack to get around not easily being able to adjust rules
			$this->durationTemp = 0;

			if(!($saved = $this->dbCallback('save')))
			{
				// put the model into the models array used for showing all errors
				$models[] = $this;
			}
		}
		
		$return = $saved & parent::updateSave($models);
		
		// clear task to human resource values to indicated secondary role
		if($this->type == 'Secondary')
		{
			$command = Yii::app()->db->createCommand("
				UPDATE `tbl_task_to_human_resource`
				SET `duration` = NULL
				WHERE `human_resource_data_id` = :human_resource_data_id");
			$command->bindParam(':human_resource_data_id', $temp = $this->human_resource_data_id);
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
		return $this->action_to_human_resource_id ? !$this->actionToHumanResource->mandatory : true;
	}

}

?>