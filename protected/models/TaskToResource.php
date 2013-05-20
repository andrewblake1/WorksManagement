<?php

/**
 * This is the model class for table "tbl_task_to_resource".
 *
 * The followings are the available columns in table 'tbl_task_to_resource':
 * @property string $id
 * @property string $task_id
 * @property integer $resource_id
 * @property string $level
 * @property string $resource_data_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property ResourceData $resourceData
 * @property ResourceData $resource
 * @property ResourceData $level0
 */
class TaskToResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchResourceToSupplier;
	public $searchTaskQuantity;
	public $searchTotalHours;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	public $quantity;
	public $hours;
	public $start;
	public $description;
	public $resource_to_supplier_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_id, resource_id, quantity, hours', 'required'),
			array('level, resource_id, quantity', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('task_id', 'length', 'max'=>10),
			array('resource_to_supplier_id', 'safe'),
			array('start, hours', 'date', 'format'=>'H:m'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, level, task_id, searchResourceToSupplier, description, quantity, searchTotalHours, searchTaskQuantity, hours, start', 'safe', 'on'=>'search'),
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
            'resource' => array(self::BELONGS_TO, 'ResourceData', 'resource_id'),
            'level0' => array(self::BELONGS_TO, 'ResourceData', 'level'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'searchResourceToSupplier' => 'Supplier',
			'resource_to_supplier_id' => 'Supplier',
			'resource_id' => 'Resource type',
			'description' => 'Resource type',
			'hours' => 'Duration (HH:mm)',
			'start' => 'Start time (HH:mm)',
			'searchTaskQuantity' => 'Task quantity',
			'searchTotalHours' => 'Total time',
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
			't.id',	// needed for delete and update buttons
			't.resource_id',
			'resource.description AS description',
			'supplier.name AS searchResourceToSupplier',
			'resourceData.quantity AS quantity',
			'task.quantity AS searchTaskQuantity',
			'resourceData.quantity * task.quantity AS searchTotalHours',
			'resourceData.hours AS hours',
			'resourceData.start AS start',
			't.level',
		);

		// where
		$criteria->compare('resource.description',$this->description,true);
		$criteria->compare('supplier.name',$this->searchResourceToSupplier,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('t.searchTaskQuantity',$this->searchTaskQuantity);
		$criteria->compare('resourceData.quantity * task.quantity',$this->searchTotalHours);
		$criteria->compare('hours',Yii::app()->format->toMysqlTime($this->hours));
		$criteria->compare('start',Yii::app()->format->toMysqlTime($this->start));
		$criteria->compare('t.level',$this->level);
		$criteria->compare('t.task_id',$this->task_id);
		
		//  with
		$criteria->with = array(
			'task',
			'resourceData',
			'resourceData.resourceToSupplier.resource',
			'resourceData.resourceToSupplier.supplier',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'description';
        $columns[] = static::linkColumn('searchResourceToSupplier', 'ResourceToSupplier', 'resource_to_supplier_id');
		$columns[] = 'quantity';
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'hours';
		$columns[] = 'searchTotalHours';
		$columns[] = 'start';
		$columns[] = 'level';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchResourceToSupplier',
			'description',
			'quantity',
			'searchTaskQuantity',
			'hours',
			'searchTotalHours',
			'start',
		);
	}
	
	static function getDisplayAttr()
	{
		return array('resourceData->resourceToSupplier->resource->description');
	}

	public function beforeSave()
	{
		$this->resourceData->resource_to_supplier_id = $this->resource_to_supplier_id;
		$this->resourceData->quantity = $this->quantity;
		$this->resourceData->hours = $this->hours;
		$this->resourceData->start = $this->start;

		return parent::beforeSave();
	}

	public function afterFind() {
		$this->resource_to_supplier_id = $this->resourceData->resource_to_supplier_id;
		$this->quantity = $this->resourceData->quantity;
		$this->hours = $this->resourceData->hours;
		$this->start = $this->resourceData->start;
		
		parent::afterFind();
	}

	public function insertResourceData()
	{
		if($this->level === null)
		{
			$this->level = Planning::planningLevelTaskInt;
		}
// TODO: a lot of this repeated in resource controller - abstract out - perhaps into PlanningController static function
		// ensure existance of a related ResourceData. First get the desired planning id which is the desired ancestor of task
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
		// try insert and catch and dump any error - will ensure existence
		try
		{
			$resourceData = new ResourceData;
			$resourceData->planning_id = $planning_id;
			$resourceData->resource_id = $this->resource_id;
			$resourceData->level = $level;
			$resourceData->quantity = $this->quantity;
			$resourceData->hours = $this->hours;
			$resourceData->start = $this->start;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$resourceData->dbCallback('save');
		}
		catch (CDbException $e)
		{
			// dump

		}
		// retrieve the ResourceData
		$resourceData = ResourceData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'resource_id'=>$this->resource_id,
		));

		// link this Resource to the ResourceData
		$this->resource_data_id = $resourceData->id;
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$saved = true;
		
		// ensure the related items are set
		$this->beforeSave();
		$oldResourceData_id = $this->resourceData->id;

		// ensure the ResourceData has correct level by inserting a new one if necassary or linking to correct
		$this->insertResourceData();

		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		$this->insertResourceData();
	
		return parent::createSave($models);
	}

}

?>