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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_task_to_action';
	}

	// needed as using a view
	public function primaryKey()
	{
		return 'id';
	}

	public function rules($ignores = array())
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return parent::rules(array('description'));
	}

	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		// elimate actions where due to mode or branching there are no duties
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
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'description' => 'Action',
			'derived_importance' => 'Importance',
		));
	}

	public function createSave(&$models=array(), $runValidation = true)
	{
		// only need to call factory method to add duties as no actual TaskToAction table
		// factory method to create duties
		 
		return Duty::addDuties($this->id = $this->action_id, $this->task, $models);
	}
	
	// again - no actual table, however need to remove duties
	public function delete() {
		$criteria = new DbCriteria();
		
		$criteria->compare('dutyStep.action_id', $this->action_id);
		$criteria->compare('task_id', $this->task_id);
		
		$criteria->with = array(
			'dutyData.dutyStep',
		);

		// must delete individually as can't join to duty data as duty contains trigger refering to dutydata - mysql limitation 1442
		foreach(Duty::model()->findAll($criteria) as $duty)
		{
			$duty->delete();
		}
	}
}

?>