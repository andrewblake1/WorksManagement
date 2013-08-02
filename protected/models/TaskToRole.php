<?php

/**
 * This is the model class for table "tbl_task_to_role".
 *
 * The followings are the available columns in table 'tbl_task_to_role':
 * @property string $id
 * @property string $task_id
 * @property string $role_data_id
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property RoleData $roleData
 * @property User $updatedBy
 */
class TaskToRole extends ActiveRecord
{
	public $searchTaskQuantity;
	public $searchMode;
	public $searchRole;
	public $searchEstimatedTotalQuantity;
	public $searchCalculatedTotalQuantity;

	public $estimated_total_quantity;
	public $searchLevel;
	public $human_resource_id;
	public $mode_id;
	public $level;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Additional HR Role';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('role_data_id')), array(
			array('human_resource_id', 'required'),
			array('level, human_resource_id, mode_id, estimated_total_quantity', 'numerical', 'integerOnly'=>true),
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
			'roleData' => array(self::BELONGS_TO, 'RoleData', 'role_data_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'human_resource_id' => 'Role',
			'estimated_total_quantity' => 'Override level quantity',
			'searchEstimatedTotalQuantity' => 'Override level quantity',
			'searchCalculatedTotalQuantity' => 'Level quantity',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchRole', $this->searchRole, 'humanResource.auth_item_name', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchTaskQuantity', $this->searchTaskQuantity, 'task.quantity');
		$criteria->compareAs('searchEstimatedTotalQuantity', $this->searchEstimatedTotalQuantity, 'roleData.estimated_total_quantity');
		$criteria->compareAs('searchCalculatedTotalQuantity', $this->searchCalculatedTotalQuantity, '(SELECT MAX(quantity) FROM tbl_task_to_role WHERE role_data_id = t.role_data_id)');

		// limit to matching task mode
		$criteria->join = "
			JOIN tbl_task task ON t.task_id = task.id
			JOIN tbl_role_data roleData ON t.role_data_id = roleData.id
			JOIN tbl_level level0 ON roleData.level = level0.id
			JOIN tbl_human_resource humanResource ON roleData.human_resource_id = humanResource.id
			JOIN tbl_mode mode
				ON roleData.mode_id = mode.id
				AND task.mode_id = roleData.mode_id
		";
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchRole';
		$columns[] = 'searchLevel';
//		$columns[] = 'searchMode';
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'searchRole',
			'searchMode',
		);
	}

	public function afterFind() {
		parent::afterFind();

		$this->estimated_total_quantity = $this->roleData->estimated_total_quantity;
		$this->human_resource_id = $this->roleData->human_resource_id;
		$this->level = $this->roleData->level;
		$this->mode_id = $this->roleData->mode_id;
		
	}

// TODO:repeated in duties
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array()/*, $taskTemplateToRole=null*/)
	{
		// ensure existance of a related RoleData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
//		$role = HumanResource::model()->findByPk($this->auth_item_name);

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

		// retrieve RoleData - or insert if doesn't exist
		if(!$roleData = RoleData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'human_resource_id'=>$this->human_resource_id,
		)))
		{
			$roleData = new RoleData;
			$roleData->planning_id = $planning_id;
			$roleData->human_resource_id = $this->human_resource_id;
			$roleData->estimated_total_quantity = $this->estimated_total_quantity;
			$roleData->level = $level;
			$roleData->mode_id = $this->task->mode_id;
			$roleData->updated_by = Yii::app()->user->id;
			$roleData->insert();
		}

		// link this HumanResource to the RoleData
		$this->role_data_id = $roleData->id;
		
		return parent::createSave($models);
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{	
		$saved = true;

		// attempt save of related RoleData
		$this->roleData->estimated_total_quantity = $this->estimated_total_quantity;
		$this->roleData->human_resource_id = $this->human_resource_id;
		$this->roleData->level = $this->level;
		if($saved &= $this->roleData->updateSave($models))
		{
			// problem here is that the the ...data may have completely changed as a result of convergence or divergence
			// due to a level change
			unset($this->role_data_id);

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