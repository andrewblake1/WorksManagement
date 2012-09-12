<?php

/**
 * This is the Nested Set  model class for table "schedule".
 *
 * The followings are the available columns in table 'schedule':
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property string $level
 * @property string $name
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Crew $crew
 * @property Day $day
 * @property DutyData[] $dutyDatas
 * @property Project $project
 * @property ResourceData[] $resourceDatas
 * @property ResourceData[] $resourceDatas1
 * @property Staff $staff
 * @property Task $task
 */
class Schedule extends CategoryActiveRecord {

	/**
	 * Data types. These are the emum values set by the DataType custom type within 
	 * the database
	 */
	const scheduleLevelProject = 'Project';
	const scheduleLevelTask = 'Task';
	const scheduleLevelDay = 'Day';
	const scheduleLevelCrew = 'Crew';

	const scheduleLevelProjectInt = 1;
	const scheduleLevelDayInt = 2;
	const scheduleLevelCrewInt = 3;
	const scheduleLevelTaskInt = 4;

	/**
	 * @return array duty level value => duty level display name
	 */
	public static function getLevels()
	{
		return array(
			Schedule::scheduleLevelProjectInt=>Schedule::scheduleLevelProject,
			Schedule::scheduleLevelDayInt=>Schedule::scheduleLevelDay,
			Schedule::scheduleLevelCrewInt=>Schedule::scheduleLevelCrew,
			Schedule::scheduleLevelTaskInt=>Schedule::scheduleLevelTask,
		);
	}

	/**
	 * @var int project_id passed from parent to identify the project to display 
	 */
	public $project_id;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id', 'safe'),
		) + parent::rules();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
 			'id' => 'Schedule',
		) + parent::attributeLabels();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'schedule';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'crew' => array(self::HAS_ONE, 'Crew', 'id'),
			'day' => array(self::HAS_ONE, 'Day', 'id'),
			'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'schedule_id'),
			'project' => array(self::HAS_ONE, 'Project', 'id'),
			'resourceDatas' => array(self::HAS_MANY, 'ResourceData', 'schedule_id'),
			'resourceDatas1' => array(self::HAS_MANY, 'ResourceData', 'level'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'task' => array(self::HAS_ONE, 'Task', 'id'),
		);
	}

// TODO: either strip this to database trigger or ideally remove the need for day->task_id and crew->day_id completely i.e. run off
// ajaxtree completely. It is possible for ajax tree not to match these parents hence needs fixing.
// another alternative is to store parent_id in the schedule table on afterSave but at time of writing not sure of effect on surroundings
	public function afterSave() {
		
		// need to ensure that the parent id stays up to date the task and crew tables. task->crew_id and crew->day_id
		// this is because it is possible to rearrange in the ajax tree
		// NB: project and day parent id's can't be mucked up in ajax tree (client_id and project_id respectively)
		switch($this->level)
		{
			case scheduleLevelCrew :
				$model=Category::model()->findByPk($this->id);
				$parent=$model->parent;
				$model->day_id = $parent->id;
				$model->save();
				break;
			case scheduleLevelTask :
				$model=Category::model()->findByPk($this->id);
				$parent=$model->parent;
				$model->crew_id = $parent->id;
				$model->save();
				break;
		}
		
		parent::afterSave();
	}

}