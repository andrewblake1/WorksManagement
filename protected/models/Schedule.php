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
 * @property integer $in_charge_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Crew $crew
 * @property Day $day
 * @property DutyData[] $dutyDatas
 * @property Project $project
 * @property ResourceData[] $resourceDatas
 * @property ResourceData[] $resourceDatas1
 * @property Staff $inCharge
 * @property Staff $staff
 * @property Task $task
 */
class Schedule extends CategoryActiveRecord {
	public $levelName;
	
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
	static $levels = array(
		self::scheduleLevelProjectInt=>self::scheduleLevelProject,
		self::scheduleLevelDayInt=>self::scheduleLevelDay,
		self::scheduleLevelCrewInt=>self::scheduleLevelCrew,
		self::scheduleLevelTaskInt=>self::scheduleLevelTask,
	);

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
			array('project_id, in_charge_id, levelName', 'safe'),
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
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge_id'),
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

	public static function printULTree($parent_id = null) {
		
		if($parent_id !== null)
		{
			$model = static::model()->findByPk($parent_id);
			$categories = $model->descendants()->findAll();
			$level = 1;
		}
		else
		{
			$categories = static::model()->findAll(array('order' => 'root,lft'));
			$level = 0;
		}

		foreach ($categories as $n => $category) {

			if ($category->level == $level)
			{
				echo CHtml::closeTag('li') . "\n";
			}
			else if ($category->level > $level)
				echo CHtml::openTag('ul') . "\n";
			else {
				echo CHtml::closeTag('li') . "\n";

				for ($i = $level - $category->level; $i; $i--) {
					echo CHtml::closeTag('ul') . "\n";
					echo CHtml::closeTag('li') . "\n";
				}
			}

			echo CHtml::openTag('li', array('id' => 'node_' . $category->id, 'rel' => $category->name));
			echo CHtml::openTag('a', array('href' => '#', 'class' => "level{$category->level}"));
			if($category->name)
			{
				$label = $category->name;
			}
			else
			{
				switch($category->level)
				{
					case Schedule::scheduleLevelDayInt :
						$label = 'Day '.++$dayCounter;
						$crewCounter = 0;
						break;
					case Schedule::scheduleLevelCrewInt :
						$label = 'Crew '.++$crewCounter;
						break;
				}
			}
			switch($category->level)
			{
				case Schedule::scheduleLevelDayInt :
					$label .= " (D{$category->id})";
					break;
				case Schedule::scheduleLevelCrewInt :
					$label .= " (C{$category->id})";
					break;
				case Schedule::scheduleLevelTaskInt :
					$label .= " (T{$category->id})";
					break;
			}
			echo CHtml::encode($label);
			echo CHtml::closeTag('a');

			$level = $category->level;
		}

		for ($i = $level; $i; $i--) {
			echo CHtml::closeTag('li') . "\n";
			echo CHtml::closeTag('ul') . "\n";
		}
	}

	public function afterFind() {
		switch($this->level)
		{
			case self::scheduleLevelProjectInt;
				$this->levelName = self::scheduleLevelProject;
				break;
			case self::scheduleLevelDayInt;
				$this->levelName = self::scheduleLevelDay;
				break;
			case self::scheduleLevelCrewInt;
				$this->levelName = self::scheduleLevelCrew;
				break;
			case self::scheduleLevelTaskInt;
				$this->levelName = self::scheduleLevelTask;
				break;
		}
		parent::afterFind();
	}
	
/*	// ensure that where possible a pk has been passed from parent
	public function assertFromParent()
	{
		// assert from parent won't work as normal from within schedule because the parents are all internal in the nested set
		// however we do need to ensure that project_id, and subsequently that client_id are set
		$project = Project::model()->findByPk($_SESSION['Schedule']['value']);
		$_SESSION['Client']['name'] = 'id';
		$_SESSION['Client']['value'] = $project->projectType->client_id;
		
		return;
	}*/	
	
}

?>