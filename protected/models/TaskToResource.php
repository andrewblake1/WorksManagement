<?php

/**
 * This is the model class for table "tbl_task_to_resource".
 *
 * The followings are the available columns in table 'tbl_task_to_resource':
 * @property string $id
 * @property string $task_id
 * @property string $resource_data_id
 * @property string $duration
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property ResourceData $resourceData
 */
class TaskToResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	public $searchResourceToSupplierId;
	public $searchResource;
	public $searchTaskQuantity;
	public $searchMode;
	public $searchEstimatedTotalDuration;
	public $searchEstimatedTotalQuantity;
	public $searchCalculatedTotalDuration;
	public $searchCalculatedTotalQuantity;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	public $estimated_total_quantity;
	public $estimated_total_duration;
	public $start;
	public $description;
	public $resource_to_supplier_id;
	public $searchLevel;
	public $resource_id;
	public $mode_id;
	public $level;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_id, resource_id, mode_id, quantity, duration', 'required'),
			array('level, resource_id, mode_id, resource_to_supplier_id, quantity, estimated_total_quantity', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			array('start, duration, estimated_total_duration', 'date', 'format'=>'H:m'),
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
            'resourceData' => array(self::BELONGS_TO, 'ResourceData', 'resource_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'searchSupplier' => 'Supplier',
			'searchResource' => 'Resource',
			'resource_to_supplier_id' => 'Supplier',
			'description' => 'Resource type',
			'searchMode' => 'Mode',
			'searchTaskQuantity' => 'Task quantity',
			'searchEstimatedTotalDuration' => 'Override level duration',
			'searchEstimatedTotalQuantity' => 'Override level quantity',
			'searchCalculatedTotalDuration' => 'Level duration',
			'searchCalculatedTotalQuantity' => 'Level quantity',
			'searchLevel' => 'Level',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.*',	// needed for after find
			'resourceData.resource_to_supplier_id AS searchResourceToSupplierId',
			'resourceData.resource_id AS resource_id',
			'resource.description AS searchResource',
			'supplier.name AS searchSupplier',
			'resourceData.start AS start',
			'level0.name AS searchLevel',
			'mode.description AS searchMode',
			'task.quantity AS searchTaskQuantity',
			'resourceData.estimated_total_quantity AS searchEstimatedTotalQuantity',
			'resourceData.estimated_total_duration AS searchEstimatedTotalDuration',
			't.duration AS duration',
			't.quantity AS quantity',
			'(SELECT MAX(quantity) AS searchCalculatedTotalQuantity FROM tbl_task_to_resource
				WHERE duty_data_id = t.duty_data_id)',
			'(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) AS searchCalculatedTotalDuration FROM tbl_task_to_resource
				WHERE duty_data_id = t.duty_data_id)',
		);

		// where
		$criteria->compare('mode.description',$this->searchMode,true);
		$criteria->compare('resource.description',$this->searchResource,true);
		$criteria->compare('supplier.name',$this->searchSupplier,true);
		$criteria->compare('t.searchTaskQuantity',$this->searchTaskQuantity);
		$criteria->compare('start',Yii::app()->format->toMysqlTime($this->start));
		$criteria->compare('level0.name',$this->searchLevel,true);
		$criteria->compare('t.task_id',$this->task_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('duration',Yii::app()->format->toMysqlTime($this->duration));
		$criteria->compare('resourceData.estimated_total_quantity',$this->searchEstimatedTotalQuantity);
		$criteria->compare('resourceData.estimated_total_duration',Yii::app()->format->toMysqlTime($this->searchEstimatedTotalDuration));
		$criteria->compare('(SELECT MAX(quantity) FROM tbl_task_to_resource
			WHERE duty_data_id = t.duty_data_id)',$this->searchEstimatedTotalQuantity);
		$criteria->compare('(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(duration) * task.quantity)) FROM tbl_task_to_resource
			WHERE duty_data_id = t.duty_data_id)',Yii::app()->format->toMysqlTime($this->searchEstimatedTotalDuration));

		// limit to matching task mode
		$criteria->join = "
			JOIN tbl_task task ON t.task_id = task.id
			JOIN tbl_resource_data resourceData ON t.resource_data_id = resourceData.id
			JOIN tbl_resource resource ON resourceData.resource_id = resource.id
			JOIN tbl_level level0 ON resourceData.level = level0.id
			JOIN tbl_mode mode
				ON resourceData.mode_id = mode.id
				AND task.mode_id = resourceData.mode_id
			LEFT JOIN tbl_resource_to_supplier resourceToSupplier ON resource.id = resourceToSupplier.resource_id
			LEFT JOIN tbl_supplier supplier ON resourceToSupplier.supplier_id = supplier.id
		";
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchResource';
        $columns[] = static::linkColumn('searchSupplier', 'ResourceToSupplier', 'searchResourceToSupplierId');
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'start:time';
		$columns[] = 'searchLevel';
		$columns[] = 'searchMode';
		$columns[] = 'quantity';
		$columns[] = 'duration:time';
		$columns[] = 'searchTotalQuantity';
		$columns[] = 'searchTotalDuration:time';
		$columns[] = 'searchCalculatedQuantity';
		$columns[] = 'searchCalculatedDuration:time';
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'resourceData->resourceToSupplier->resource->description',
			'resourceData->mode->description',
		);
	}

	public function beforeSave()
	{
		$this->resourceData->estimated_total_quantity = $this->estimated_total_quantity;
		$this->resourceData->estimated_total_duration = $this->estimated_total_duration;
		$this->resourceData->start = $this->start;
		$this->resourceData->resource_id = $this->resource_id;
		$this->resourceData->level = $this->level;
		$this->resourceData->mode_id = $this->mode_id;

		return parent::beforeSave();
	}

	public function afterFind() {
		parent::afterFind();

		$this->resource_to_supplier_id = $this->resourceData->resource_to_supplier_id;
		$this->estimated_total_quantity = $this->resourceData->estimated_total_quantity;
		$this->estimated_total_duration = $this->resourceData->estimated_total_duration;
		$this->start = $this->resourceData->start;
		$this->resource_id = $this->resourceData->resource_id;
		$this->level = $this->resourceData->level;
		$this->mode_id = $this->resourceData->mode_id;
		
	}

// TODO:repeated in duties
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array(), $taskTemplateToResource=null)
	{
		// ensure existance of a related ResourceData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
		$resource = Resource::model()->findByPk($this->resource_id);

		if(($level = $resource->level) == Planning::planningLevelTaskInt)
		{
			$planning_id = $this->task_id;
		}
		else
		{
			// get the desired ansestor
			$planning = Planning::model()->findByPk($this->task_id);

			while($planning = $planning->parent)
			{
				if($planning->level == $level)
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

		// retrieve ResourceData - or insert if doesn't exist
		if(!$resourceData = ResourceData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'resource_id'=>$resource->id,
		)))
		{
			$resourceData = new ResourceData;
			$resourceData->planning_id = $planning_id;
			$resourceData->resource_id = $this->resource_id;
			$resourceData->level = $level;
			$resourceData->resource_to_supplier_id = $this->resource_to_supplier_id;
			$resourceData->estimated_total_quantity = $this->estimated_total_quantity;
			$resourceData->estimated_total_duration = $this->estimated_total_duration;
			$resourceData->start = $this->start;
			$resourceData->mode_id = $this->mode_id;
			$resourceData->updated_by = Yii::app()->user->id;
			$resourceData->insert();
		}

		// link this Resource to the ResourceData
		$this->resource_data_id = $resourceData->id;
		
		return parent::createSave($models);
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{	
		$saved = true;
		$this->resourceData->attributes = $_POST['ResourceData'];

		// attempt save of related ResourceData
		if($saved &= $this->resourceData->updateSave($models))
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