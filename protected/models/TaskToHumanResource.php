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
	public $description;
	public $human_resource_to_supplier_id;
	public $searchLevel;
	public $human_resource_id;
	public $mode_id;
	public $level;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('human_resource_id, mode_id', 'required'),
			array('level, human_resource_id, mode_id, human_resource_to_supplier_id, estimated_total_quantity', 'numerical', 'integerOnly'=>true),
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
			'description' => 'HumanResource',
			'estimated_total_duration' => 'Override level duration',
			'estimated_total_quantity' => 'Override level quantity',
			'searchEstimatedTotalDuration' => 'Override level duration',
			'searchEstimatedTotalQuantity' => 'Override level quantity',
			'searchCalculatedTotalDuration' => 'Level duration',
			'searchCalculatedTotalQuantity' => 'Level quantity',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchHumanResource', $this->searchHumanResource, 'humanResource.description', true);
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
			JOIN tbl_mode mode
				ON humanResourceData.mode_id = mode.id
				AND task.mode_id = humanResourceData.mode_id
			LEFT JOIN tbl_human_resource_to_supplier resourceToSupplier ON humanResource.id = resourceToSupplier.human_resource_id
			LEFT JOIN tbl_supplier supplier ON resourceToSupplier.supplier_id = supplier.id
		";
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchHumanResource';
        $columns[] = static::linkColumn('searchSupplier', 'HumanResourceToSupplier', 'searchHumanResourceToSupplierId');
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'start:time';
		$columns[] = 'searchLevel';
		$columns[] = 'searchMode';
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
		$this->human_resource_id = $this->humanResourceData->human_resource_id;
		$this->level = $this->humanResourceData->level;
		$this->mode_id = $this->humanResourceData->mode_id;
		
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
			$humanResourceData->start = $this->start;
			$humanResourceData->mode_id = $this->mode_id;
			$humanResourceData->updated_by = Yii::app()->user->id;
			$humanResourceData->insert();
		}

		// link this HumanResource to the HumanResourceData
		$this->human_resource_data_id = $humanResourceData->id;
		
		return parent::createSave($models);
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{	
		$saved = true;

		// attempt save of related HumanResourceData
		$this->humanResourceData->estimated_total_quantity = $this->estimated_total_quantity;
		$this->humanResourceData->estimated_total_duration = $this->estimated_total_duration;
		$this->humanResourceData->start = $this->start;
		$this->humanResourceData->human_resource_id = $this->human_resource_id;
		$this->humanResourceData->level = $this->level;
		$this->humanResourceData->mode_id = $this->mode_id;
		if($saved &= $this->humanResourceData->updateSave($models))
		{
			if(!$saved = $this->dbCallback('save'))
			{
				// put the model into the models array used for showing all errors
				$models[] = $this;
			}
		}
		
		return $saved & parent::updateSave($models);
	}

}

?>