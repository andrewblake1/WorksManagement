<?php

/**
 * This is the model class for table "v_task_to_action".
 *
 * The followings are the available columns in table 'v_task_to_action':
 * @property string $id
 * @property string $action_id
 * @property integer $client_id
 * @property integer $project_template_id
 * @property string $override_id
 * @property string $description
 * @property string $task_id
 * @property integer $updated_by
 * @property string $derived_importance
 */
class TaskToAction extends ViewActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Action';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_task_to_action';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('action_id, task_id', 'required'),
            array('client_id, project_template_id', 'numerical', 'integerOnly'=>true),
            array('action_id, override_id, task_id', 'length', 'max'=>10),
            array('description', 'length', 'max'=>255),
            array('derived_importance', 'length', 'max'=>8),
			array('derived_importance, description, task_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',
			't.description',
			't.derived_importance',
			't.task_id',
		);
		
		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.derived_importance',$this->derived_importance,true);
		$criteria->compare('t.task_id',$this->task_id);
		
		// elimate actions where due to mode or branching there are no duties
//		ViewDuty::createTmpDuty();
		$criteria->distinct = TRUE;
		$criteria->join = "
			JOIN v_duty duty
				ON t.action_id = duty.action_id
				AND t.task_id = duty.task_id
		";

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'derived_importance';
		
		return $columns;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'description' => 'Action',
			'derived_importance' => 'Importance',
		));
	}

	public function createSave(&$models=array())
	{
		// only need to call factory method to add duties as no actual TaskToAction table
		// factory method to create duties
		 
		return Duty::addDuties($this->id = $this->action_id, $this->task_id, $models);
	}
	
	// again - no actual table, however need to remove duties
	public function delete() {
		return Duty::deleteAllByAttributes(array(
			'action_id' => $this->action_id,
			'task_id' => $this->task_id,
		));
	}
}

?>