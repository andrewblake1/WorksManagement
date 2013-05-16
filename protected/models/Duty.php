<?php

/**
 * This is the model class for table "tbl_duty".
 *
 * The followings are the available columns in table 'tbl_duty':
 * @property string $id
 * @property string $task_id
 * @property integer $duty_type_id
 * @property string $duty_data_id
 * @property integer $responsible
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property DutyData $dutyType
 * @property User $responsible0
 * @property DutyData $dutyData
 */
class Duty extends ActiveRecord
{
	public $assignedTo;
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $description;
	public $searchInCharge;
	public $searchImportance;
	public $custom_value_id;
	public $updated;
	public $due;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, duty_type_id', 'required'),
			array('duty_type_id, responsible', 'numerical', 'integerOnly'=>true),
			array('task_id, duty_data_id', 'length', 'max'=>10),
			array('updated, custom_value_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, due, searchImportance, searchInCharge, searchTask, description, updated', 'safe', 'on'=>'search'),
		);
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
            'dutyType' => array(self::BELONGS_TO, 'DutyData', 'duty_type_id'),
            'responsible0' => array(self::BELONGS_TO, 'User', 'responsible'),
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
			'searchTask' => 'Task',
			'duty_type_id' => 'Duty/Role/First/Last/Email',
			'description' => 'Duty',
			'responsible' => 'Assigned to',
			'updated' => 'Completed',
			'custom_value_id' => 'Custom value',
			'searchInCharge' => 'Assigned to',
			'searchImportance' => 'Importance',
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
			't.id',	// needed for delete and update buttons
			'dutyType.description AS description',
			'(SELECT `date` FROM tbl_working_days WHERE id = (SELECT id - lead_in_days FROM tbl_working_days WHERE `date` <= day.scheduled ORDER BY id DESC LIMIT 1)) as due',
			"COALESCE(
				IF(LENGTH(CONCAT_WS('$delimiter',
					responsible.first_name,
					responsible.last_name,
					responsible.email
					))=0, NULL, CONCAT_WS('$delimiter',
					responsible.first_name,
					responsible.last_name,
					responsible.email
					)),
				IF(LENGTH(CONCAT_WS('$delimiter',
					dutyDefault.first_name,
					dutyDefault.last_name,
					dutyDefault.email
					))=0, NULL, CONCAT_WS('$delimiter',
					dutyDefault.first_name,
					dutyDefault.last_name,
					dutyDefault.email
					)),
				CONCAT_WS('$delimiter',
					inCharge.first_name,
					inCharge.last_name,
					inCharge.email
					)
				) AS searchInCharge",
			'dutyData.updated AS updated',
			'taskTemplateToDutyType.importance AS searchImportance',
		);

		// where
		$criteria->compare('dutyType.description',$this->description,true);
		$criteria->compare('taskTemplateToDutyType.importance',$this->searchImportance,true);
		$this->compositeCriteria(
			$criteria,
			array(
				'dutyDefault.first_name',
				'dutyDefault.last_name',
				'dutyDefault.email',
			), $this->searchInCharge);
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);
		$criteria->compare('updated',Yii::app()->format->toMysqlDateTime($this->updated));
		$criteria->compare('t.task_id',$this->task_id);

		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;

		// join
		$criteria->join = '
			JOIN tbl_task task ON t.task_id = task.id
			JOIN tbl_project project ON task.project_id = project.id
			JOIN tbl_crew crew ON task.crew_id = crew.id
			JOIN tbl_day day ON crew.day_id = day.id
			JOIN tbl_duty_type dutyType ON t.duty_type_id = dutyType.id
			LEFT JOIN tbl_task_template_to_duty_type taskTemplateToDutyType USING ( task_template_id, duty_type_id )
			LEFT JOIN tbl_project_template_to_auth_item projectTemplateToAuthItem ON taskTemplateToDutyType.project_template_to_auth_item_id = projectTemplateToAuthItem.id
			LEFT JOIN tbl_project_to_project_template_to_auth_item projectToProjectTemplateToAuthItem ON projectTemplateToAuthItem.id = projectToProjectTemplateToAuthItem.project_template_to_auth_item_id
			LEFT JOIN AuthAssignment ON projectToProjectTemplateToAuthItem.auth_assignment_id = AuthAssignment.id
			LEFT JOIN tbl_user dutyDefault ON AuthAssignment.userid = dutyDefault.id
			LEFT JOIN tbl_user responsible ON t.responsible = responsible.id
		';
		
		// with
		$criteria->with = array(
			'dutyData',
			'dutyData.planning.inCharge',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
        $columns[] = static::linkColumn('searchInCharge', 'User', 'assignedTo');
        $columns[] = 'searchImportance';
		$columns[] = 'due:date';
		$columns[] = 'updated:datetime';

		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'dutyType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'description', 'updated', 'searchInCharge', 'searchImportance', 'due');
	}

	public function beforeValidate()
	{
//		if(isset($this->taskTemplateToDutyType_id))
//		{
//			$model = TaskTemplateToDutyType::model()->findByPk($this->taskTemplateToDutyType_id);
//			$this->task_template_id = $model->task_template_id ;
//			$this->duty_type_id = $model->duty_type_id ;
//		}
		
		return parent::beforeValidate();
	}

	public function afterFind() {

		$this->updated = $this->dutyData->updated;
		
		parent::afterFind();
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$saved = true;
		$this->dutyData->updated = $this->updated;

		// if we need to update a customValue
		if($customValue = $this->dutyData->customValue)
		{
			// massive assignement
			$customValue->attributes=$_POST['CustomValue'][$customValue->id];

			// validate and save
			$saved &= $customValue->updateSave($models, array(
				'customField' => $this->taskTemplateToDutyType->dutyType->customField,
				'params' => array('relationToCustomField'=>'duty->taskTemplateToDutyType->dutyType->customField'),
			));
		}

		// attempt save of related DutyData
		$saved &= $this->dutyData->updateSave($models);
		
		return $saved & parent::updateSave($models);
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		$saved = true;
		
		// ensure existance of a related DutyData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
		if(($level = $this->dutyType->level) == Planning::planningLevelTaskInt)
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
			$dutyData = new DutyData;
			$dutyData->planning_id = $planning_id;
			$dutyData->duty_type_id = $this->duty_type_id;
			$dutyData->level = $level;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$dutyData->dbCallback('save');
		}
		catch (CDbException $e)
		{
			// dump

		}
		// retrieve the DutyData
		$dutyData = DutyData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'duty_type_id'=>$this->duty_type_id,
		));

		// if there isn't already a customValue item to hold value and there should be
		if(empty($dutyData->customValue) && !empty($this->dutyType->custom_field_id))
		{
			// create a new customValue item to hold value
			$saved &= CustomValue::createCustomField($this->dutyType, $models, $customValue);
			// associate the new customValue to this duty
			$dutyData->custom_value_id = $customValue->id;
			// attempt save
			$saved &= $dutyData->createSave($models);
		}

		// link this Duty to the DutyData
		$this->duty_data_id = $dutyData->id;

		return $saved & parent::createSave($models);
	}

}

?>