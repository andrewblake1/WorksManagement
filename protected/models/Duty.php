<?php

/**
 * This is the model class for table "tbl_duty".
 *
 * The followings are the available columns in table 'tbl_duty':
 * @property string $id
 * @property string $task_id
 * @property string $duty_data_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property DutyData $dutyData
 */
class Duty extends CustomFieldActiveRecord
{
	protected $defaultSort = array('t.lead_in_days'=>'DESC');
	
	public $derived_assigned_to_id;
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $description;
	public $derived_assigned_to_name;
	
	public $updated;
	public $due;
	
	public $duty_step_id;
	public $responsible;
	public $task_to_action_id;
	public $action_id;
	public $lead_in_days;
	
	/**
	 * @var string label on button in update view
	 */
	static $updateButtonText;
	
	// CustomFieldActiveRecord
	protected $evalCustomFieldPivots = '$this->dutyData->dutyStep->customFieldToDutySteps';
	protected $evalClassEndToCustomFieldPivot = 'DutyDataToCustomFieldToDutyStep';
	protected $evalColumnCustomFieldModelTemplateId = 'custom_field_to_duty_step_id';
	protected $evalColumnEndId = 'duty_data_id';
	protected $evalEndToCustomFieldPivots = '$this->dutyData->dutyDataToCustomFieldToDutySteps';
	protected $evalCustomFieldPivot = 'customFieldToDutyStep';
	protected $evalThisColumnEndId = 'duty_data_id';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_id', 'required'),
			array('duty_step_id, responsible', 'numerical', 'integerOnly'=>true),
			array('task_id, duty_data_id', 'length', 'max'=>10),
			array('action_id, updated', 'safe'),
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
            'dutyData' => array(self::BELONGS_TO, 'DutyData', 'duty_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'duty_step_id' => 'Duty',
			'description' => 'Duty',
			'responsible' => 'Assigned to',
			'updated' => 'Completed',
			'derived_assigned_to_name' => 'Assigned to',
			'project_name' => 'Project',
			'action_description' => 'Action',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		// NB: taking first non null of either the user assigned to duty at project or user in charge at target duty level
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.*',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.derived_assigned_to_name',$this->derived_assigned_to_name,true);
		$criteria->compare('t.updated',Yii::app()->format->toMysqlDateTime($this->updated));
		$criteria->compare('t.due',Yii::app()->format->toMysqlDateTime($this->due));
		$criteria->compare('t.task_id',$this->task_id);
		$criteria->compare('t.lead_in_days',$this->lead_in_days);
		$criteria->compare('t.action_id',$this->action_id);
		
		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
        $columns[] = static::linkColumn('derived_assigned_to_name', 'User', 'derived_assigned_to_id');
		$columns[] = 'due:date';
		$columns[] = 'updated:datetime';
		$columns[] = 'lead_in_days';

		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'dutyData->dutyStep->description',
		);
	}

	public function afterFind() {

		$this->updated = $this->dutyData->updated;
		$this->responsible = $this->dutyData->responsible;
		$this->action_id = $this->dutyData->dutyStep->action_id;
		$this->duty_step_id = $this->dutyData->dutyStep->id;
		if(!$this->derived_assigned_to_id)
		{
			$this->derived_assigned_to_id = 1;
		}

		parent::afterFind();
	}

	public function getIncompleteDependencies()
	{
		// get any incomplete children
		$criteria = new DbCriteria;
		
		$criteria->select = array(
			't.id',
			'dutyChild.description AS description',
			'dutyChild.derived_assigned_to_name AS derived_assigned_to_name',
			'dutyChild.due AS due',
		);
		
		$criteria->join="
			JOIN tbl_duty_step_dependency dutyStepDependency ON t.duty_step_id = dutyStepDependency.parent_duty_step_id
			JOIN v_duty dutyChild
				ON dutyStepDependency.child_duty_step_id = dutyChild.duty_step_id 
				AND t.task_id = dutyChild.task_id
		";
		
		// this duties id
		$criteria->compare('t.id', $this->id);
		// child duties update values arn't set
		$criteria->compareNull('dutyChild.updated');

		return $criteria;
	}

	public function getImmediateDependencies()
	{
		// get any incomplete children
		$criteria = new DbCriteria;
		
		$criteria->select = array(
			'dutyChild.*',
		);
		
		$criteria->join="
			JOIN tbl_duty_data dutyData ON t.duty_data_id = dutyData.id
			JOIN tbl_duty_step_dependency dutyStepDependency ON dutyData.duty_step_id = dutyStepDependency.parent_duty_step_id
			JOIN v_duty dutyChild
				ON dutyStepDependency.child_duty_step_id = dutyChild.duty_step_id 
				AND t.task_id = dutyChild.task_id
		";
		
		$criteria->distinct = true;

		// this duties id
		$criteria->compare('t.id', $this->id);

		return $criteria;
	}

	/* 
	 * factory method for creating Duties based on actionid and task id
	 */
	public static function addDuties($actionId, $taskId, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// get the action
		$action = Action::model()->findByPk($actionId);
	
		// loop thru steps of the Action
		foreach($action->dutySteps as $dutyStep)
		{
			// create a new duty
			$duty = new Duty();
			// copy any useful attributes from
			$duty->task_id = $taskId;
			$duty->duty_step_id = $dutyStep->id;
			$saved &= $duty->createSave($models);
		}
		
		return $saved;
	}

	public static function getParentForeignKey($referencesModel, $foreignKeys = array()) {
		// No actual TaskToAdmin - actual parent is task
		if($referencesModel == 'TaskToAction')
		{
			return 'task_to_action_id';
		}
		
		return parent::getParentForeignKey($referencesModel, $foreignKeys);
	}

	/*
	 * Need to override becuase parent is TaskToAction which doesn't exist
	 */
	public function assertFromParent($modelName = null)
	{
		Controller::setAdminParam('task_id', $this->task_id, 'Duty');
		Controller::setAdminParam('action_id', $this->action_id, 'Duty');

		Controller::setUpdateId($this->task_id, 'Task');
		Controller::setUpdateId($this->action_id, 'TaskToAction');

		Controller::setAdminParam('task_id', $this->task_id, 'TaskToAction');

		$this->task->assertFromParent();
		
		// assert the task
		return $this->task->assertFromParent();
	}

	public function checkAccess($mode, $model = NULL)
	{
		
		if($model === NULL)
		{
			$model = $this;
		}
		else
		{
			$this->attributes = $model->attributes;
			$this->id = $model->id;
		}
		
		$user = User::model()->findByPk(Yii::app()->user->id);
$incompleteDependencies = $this->incompleteDependencies;
		if(Yii::app()->user->checkAccess('system admin'))
		{
			return true;
		}
		// otherwise if there is something this is relying on that hasn't been completed yet
		elseif(ViewDuty::model()->findAll($incompleteDependencies = $this->incompleteDependencies))
		{
			return $mode == Controller::accessWrite ? false : true;
		}
		// there is nothing this is dependant on so technically can be ticked off
		else
		{
			// if write access all duties, or write access on this duty
			return Yii::app()->user->checkAccess($mode == Controller::accessWrite ? 'Duty' : 'DutyRead') || ViewDuty::model()->findByAttributes(array(
				"duty_data_id"=>$this->duty_data_id,
				"derived_assigned_to_id"=>$user->contact_id,
			));
		}
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$saved = true;
		$this->dutyData->attributes = $_POST['DutyData'];
		
		// attempt save of related DutyData
		if($saved &= $this->dutyData->updateSave($models))
		{
			// duty data_id may well have changed in the database so re-read this for this this model
			$duty = Duty::model()->findByPk($this->id);
			$this->duty_data_id = $duty->duty_data_id;
			if(!$saved = $this->dbCallback('save'))
			{
				// put the model into the models array used for showing all errors
				$models[] = $this;
			}
		}
		
		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		// ensure existance of a related DutyData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
		$dutyStep = DutyStep::model()->findByPk($this->duty_step_id);

		if(($level = $dutyStep->level) == Planning::planningLevelTaskInt)
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

		// retrieve or create
		if(!$dutyData = DutyData::model()->findByAttributes(array(
				'planning_id'=>$planning_id,	
				'duty_step_id'=>$dutyStep->id,
			)))
		{
			$dutyData = new DutyData;
			$dutyData->planning_id = $planning_id;
			$dutyData->duty_step_id = $this->duty_step_id;
			$dutyData->level = $level;
			$dutyData->responsible = NULL;
			$dutyData->updated = NULL;
			$dutyData->updated_by = Yii::app()->user->id;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$dutyData->insert();
		}

		// link this Duty to the DutyData
		$this->duty_data_id = $dutyData->id;
		
		return parent::createSave($models);
	}
	
	// this needs overriding here as really should be part of duty data however handling the variables at this level
	protected function createCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;

		// loop thru all custom fields pivots
		foreach(eval("return {$this->evalCustomFieldPivots};") as $customFieldPivot)
		{
			$endToCustomFieldPivot = new $this->evalClassEndToCustomFieldPivot;
			$endToCustomFieldPivot->{$this->evalColumnCustomFieldModelTemplateId} = $customFieldPivot->id;

			$endToCustomFieldPivot->{$this->evalColumnEndId} = $this->{$this->evalThisColumnEndId};
			if(isset($_POST[get_class($endToCustomFieldPivot)][$endToCustomFieldPivot->{$this->evalCustomFieldPivot}->custom_field_id]['custom_value']))
			{
				$endToCustomFieldPivot->custom_value=$_POST[get_class($endToCustomFieldPivot)][$endToCustomFieldPivot->{$this->evalCustomFieldPivot}->custom_field_id]['custom_value'];
			}
			else
			{
				$endToCustomFieldPivot->setDefault($customFieldPivot->customField);
			}

			// attempt save
			// try insert and catch and dump any error - will ensure existence
			try
			{
				// calling insert here and dumping any errors as will error because duty_data shared over numerous duties
				// TODO: duty_data should extend customfieldactiverecord and not duty!!
				$endToCustomFieldPivot->insert();
				// record any errors
//				$models[] = $endToCustomFieldPivot;
			}
			catch (CDbException $e)
			{
				// just loose the error - don't really want to update the custom field as exis may already contain good data
				// already exists so retrieve and update instead
	/*			$exisEndToCustomFieldPivot = $evalClassEndToCustomFieldPivot::model()->findByAttibutes(array(
					'custom_field_to_duty_step_id'=>$endToCustomFieldPivot->custom_field_to_duty_step_id,
					'duty_data_id'=>$endToCustomFieldPivot->duty_data_id,
				));*/
			}
		}
		
		return $saved;
	}

}

?>