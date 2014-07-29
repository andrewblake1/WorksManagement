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
//	public $task_to_action_id;
	public $action_id;
	public $lead_in_days;

	/**
	 * @var string label on button in update view
	 */
	static $updateButtonText;

	// CustomFieldActiveRecord
	protected $evalCustomFieldPivots = '$this->dutyData->dutyStep->dutyStepToCustomFields';
	protected $evalClassEndToCustomFieldPivot = 'DutyDataToDutyStepToCustomField';
	protected $evalColumnCustomFieldModelTemplateId = 'duty_step_to_custom_field_id';
	protected $evalColumnEndId = 'duty_data_id';
	protected $evalEndToCustomFieldPivots = '$this->dutyData->dutyDataToDutyStepToCustomFields';
	protected $evalCustomFieldPivot = 'dutyStepToCustomField';
	protected $evalThisColumnEndId = 'duty_data_id';

	public function tableName() {

		return ($this->scenario == 'search') || static::$inSearch
			? 'v_duty'
			: 'tbl_duty';
	}

	// needed as using a view
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(array('duty_data_id', 'id')), array(
			array('id, action_id, duty_step_id, responsible', 'numerical', 'integerOnly'=>true),
			array('duty_data_id', 'safe'),
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
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
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
	public function getAdminColumns()
	{
        $columns[] = 'description';
        $columns[] = static::linkColumn('derived_assigned_to_name', 'User', 'derived_assigned_to_id');
		$columns[] = 'due:date';
		$columns[] = 'updated:datetime';
		$columns[] = 'lead_in_days';

		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'description',
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

	/**
	 * 
	 * @param type $booeanAnswer	set to to true if just checking existance - quicker query
	 * @return \DbCriteria
	 */
	public function getIncompleteDependencies($booeanAnswer = false)
	{
		$criteria = new DbCriteria;
		
		$criteria->select = array(
			'duty.id',
			'duty.duty_data_id',
		);
	
		// NB: the depth clause in the join is to eliminate loop back dependencies i.e. exclude them from this
		$criteria->join="
			JOIN tbl_duty_data ON t.duty_data_id = tbl_duty_data.id
			JOIN tbl_duty_step_dependency dutyStepDependency
				ON tbl_duty_data.duty_step_id = dutyStepDependency.parent_duty_step_id
				AND (SELECT MIN(depth) FROM tbl_duty_step_dependency WHERE child_duty_step_id = dutyStepDependency.child_duty_step_id) >= dutyStepDependency.depth
			JOIN tbl_duty_data dutyData ON dutyStepDependency.child_duty_step_id = dutyData.duty_step_id
			JOIN tbl_duty_step dutyStep ON dutyData.duty_step_id = dutyStep.id
			JOIN tbl_duty duty ON dutyData.id = duty.duty_data_id AND t.task_id = duty.task_id
		";
		
		// this duties id
		$criteria->compare('t.id', $this->id);
		// child duties update values arn't set
		$criteria->compareNull('dutyData.updated');
			
		if(!$booeanAnswer)
		{
			$criteria->select = array_merge($criteria->select, array(
				'dutyStep.description AS description',
				'(SELECT `date` FROM tbl_working_days w WHERE w.id = (SELECT id - dutyStep.lead_in_days FROM tbl_working_days WHERE `date` <= day.scheduled ORDER BY id DESC LIMIT 1)) as due',
				"COALESCE(
					IF(LENGTH(CONCAT_WS(' ',
						responsibleContact.id,
						responsibleContact.last_name,
						responsibleContact.email
						))=0, NULL, CONCAT_WS(' ',
						responsibleContact.first_name,
						responsibleContact.last_name,
						responsibleContact.email
						)),
					IF(LENGTH(CONCAT_WS(' ',
						dutyDefaultContact.first_name,
						dutyDefaultContact.last_name,
						dutyDefaultContact.email
						))=0, NULL, CONCAT_WS(' ',
						dutyDefaultContact.first_name,
						dutyDefaultContact.last_name,
						dutyDefaultContact.email
						)),
					CONCAT_WS(' ',
						contact.first_name,
						contact.last_name,
						contact.email
						)
					) AS derived_assigned_to_name",
			));

			$criteria->join .="
				JOIN tbl_task task ON duty.task_id = task.id
				JOIN tbl_crew crew ON task.crew_id = crew.id
				JOIN tbl_day day ON crew.day_id = day.id
				JOIN tbl_duty_step_to_mode dutyStepToMode ON dutyStep.id = dutyStepToMode.duty_step_id AND task.mode_id = dutyStepToMode.mode_id
				JOIN tbl_planning planning ON dutyData.planning_id = planning.id
				LEFT JOIN tbl_task_template_to_action taskTemplateToAction
					ON task.task_template_id = taskTemplateToAction.task_template_id
					AND dutyStep.action_id = taskTemplateToAction.action_id
				LEFT JOIN tbl_project_to_auth_item projectToAuthItem
					ON day.project_id = projectToAuthItem.project_id
					AND dutyStep.auth_item_name = projectToAuthItem.auth_item_name
				LEFT JOIN tbl_project_to_auth_item_to_auth_assignment projectToAuthItemToAuthAssignment
					ON projectToAuthItem.id = projectToAuthItemToAuthAssignment.project_to_auth_item_id
				LEFT JOIN AuthAssignment ON projectToAuthItemToAuthAssignment.auth_assignment_id = AuthAssignment.id
				LEFT JOIN tbl_user dutyDefault ON AuthAssignment.userid = dutyDefault.id
				LEFT JOIN tbl_contact dutyDefaultContact ON dutyDefault.contact_id = dutyDefaultContact.id
				LEFT JOIN tbl_user responsible ON dutyData.responsible = responsible.id
				LEFT JOIN tbl_contact responsibleContact ON responsible.contact_id = responsibleContact.id
				LEFT JOIN tbl_user inCharge ON planning.in_charge_id = inCharge.id
				LEFT JOIN tbl_contact contact ON inCharge.contact_id = contact.id
			";

			$criteria->group = "dutyData.id";
		}

		return $criteria;
	}

	public function getImmediateDependencies()
	{
		$criteria = new DbCriteria;
		
		$criteria->select = array(
			'dutyChild.*',
		);
		
		$criteria->join="
			JOIN tbl_duty_data dutyData ON t.duty_data_id = dutyData.id
			JOIN tbl_duty_step_dependency dutyStepDependency
				ON dutyData.duty_step_id = dutyStepDependency.parent_duty_step_id
				AND (SELECT MIN(depth) FROM tbl_duty_step_dependency WHERE child_duty_step_id = dutyStepDependency.child_duty_step_id) >= dutyStepDependency.depth
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
	public static function addDuties($actionId, $task, &$models=array())
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
			$duty->task_id = $task->id;
			$duty->duty_step_id = $dutyStep->id;
			$saved &= $duty->createSave($models);
		}
			
		// factory methods add related resoruces
		$saved &= ActionToLabourResource::addLabourResources($actionId, $task, $models);
		$saved &= ActionToPlant::addPlants($actionId, $task, $models);

		return $saved;
	}

	public static function getParentForeignKey($referencesModel, $foreignKeys = array()) {
		// No actual TaskToAdmin - actual parent is task
		if($referencesModel == 'TaskToAction')
		{
			return;//task_to_action_id';
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

		if(Yii::app()->user->checkAccess('system admin'))
		{
			return true;
		}
		// otherwise if there is something this is relying on that hasn't been completed yet
		elseif(DashboardDuty::model()->findAll($incompleteDependencies = $this->getIncompleteDependencies(true)))
		{
			return $mode == Controller::accessWrite ? false : true;
		}
		// there is nothing this is dependant on so technically can be ticked off
		else
		{
			// if write access all duties, or write access on this duty
			return Yii::app()->user->checkAccess($mode == Controller::accessWrite ? 'Duty' : 'DutyRead') || Duty::model()->findByAttributes(array(
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
			// problem here is that the the ...data may have completely changed as a result of convergence or divergence
			// due to a level change
			unset($this->duty_data_id);

			// save this duty
			if(!$saved = $this->dbCallback('save'))
			{
				// put the model into the models array used for showing all errors
				$models[] = $this;
			}
			else
			{
				// clear any branches above - for purpose of loop back. Can't do this within trigger as
				// updating the same table the triger is declared in - which is not allowed
				$command=Yii::app()->db->createCommand('CALL pro_loopback(:duty_step_id, :task_id)');
				$command->bindParam(":duty_step_id", $temp1 = $this->dutyData->duty_step_id);
				$command->bindParam(":task_id", $temp2 = $this->task_id);
				$command->execute();
			}
		}
	
		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array(), $runValidation = true)
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

/*$t = $this->attributes;
$t1 = $this->dutyData;
$t2 = $this->dutyData->attributes;
$t3 = $this->dutyData->dutyStep;
$t4 = $this->dutyData->attributes;
$t5 = $t3->dutyStepToCustomFields;
//$this->dutyData->dutyStep->dutyStepToCustomFields;*/
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
					'duty_step_to_custom_field_id'=>$endToCustomFieldPivot->duty_step_to_custom_field_id,
					'duty_data_id'=>$endToCustomFieldPivot->duty_data_id,
				));*/
			}
		}
		
		return $saved;
	}

}

?>