<?php

/**
 * This is the Nested Set  model class for table "planning".
 *
 * The followings are the available columns in table 'planning':
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
class Planning extends CategoryActiveRecord {
	public $levelName;
	
	/**
	 * Data types. These are the emum values set by the DataType custom type within 
	 * the database
	 */
	const planningLevelProject = 'Project';
	const planningLevelTask = 'Task';
	const planningLevelDay = 'Day';
	const planningLevelCrew = 'Crew';

	const planningLevelProjectInt = 1;
	const planningLevelDayInt = 2;
	const planningLevelCrewInt = 3;
	const planningLevelTaskInt = 4;

	/**
	 * @return array duty level value => duty level display name
	 */
	static $levels = array(
		self::planningLevelProjectInt=>self::planningLevelProject,
		self::planningLevelDayInt=>self::planningLevelDay,
		self::planningLevelCrewInt=>self::planningLevelCrew,
		self::planningLevelTaskInt=>self::planningLevelTask,
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
 			'id' => 'Planning',
		) + parent::attributeLabels();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'planning';
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
			'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'planning_id'),
			'project' => array(self::HAS_ONE, 'Project', 'id'),
			'resourceDatas' => array(self::HAS_MANY, 'ResourceData', 'planning_id'),
			'resourceDatas1' => array(self::HAS_MANY, 'ResourceData', 'level'),
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'task' => array(self::HAS_ONE, 'Task', 'id'),
		);
	}

// TODO: either strip this to database trigger or ideally remove the need for day->task_id and crew->day_id completely i.e. run off
// ajaxtree completely. It is possible for ajax tree not to match these parents hence needs fixing.
// another alternative is to store parent_id in the planning table on afterSave but at time of writing not sure of effect on surroundings
	public function afterSave() {
		
		// need to ensure that the parent id stays up to date the task and crew tables. task->crew_id and crew->day_id
		// this is because it is possible to rearrange in the ajax tree
		// NB: project and day parent id's can't be mucked up in ajax tree (client_id and project_id respectively)
		switch($this->level)
		{
			case planningLevelCrew :
				$model=Category::model()->findByPk($this->id);
				$parent=$model->parent;
				$model->day_id = $parent->id;
				$model->save();
				break;
			case planningLevelTask :
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
			switch($category->level)
			{
				case Planning::planningLevelDayInt :
					$day = Day::model()->findByPk($category->id);
					++$dayCounter;
					$label = empty($day->scheduled) ? "Day $dayCounter" : $day->scheduled;
					$label .= " (D{$category->id})";
					$crewCounter = 0;
					break;
				case Planning::planningLevelCrewInt :
					$crew =Crew::model()->findByPk($category->id);
					++$crewCounter;
					$label = empty($category->in_charge_id) ? "Crew $crewCounter" : Crew::getNiceName(null, $crew);
					$label .= " (C{$category->id})";
					break;
				case Planning::planningLevelTaskInt :
					$label = $category->name;
					$label .= " (T{$category->id})";
					break;
				default :
					$label = $category->name;
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
			case self::planningLevelProjectInt;
				$this->levelName = self::planningLevelProject;
				break;
			case self::planningLevelDayInt;
				$this->levelName = self::planningLevelDay;
				break;
			case self::planningLevelCrewInt;
				$this->levelName = self::planningLevelCrew;
				break;
			case self::planningLevelTaskInt;
				$this->levelName = self::planningLevelTask;
				break;
		}
		parent::afterFind();
	}
	

	public function beforeSave() {
		
		// if user doesn't have scheduler priveledge
		if(!Yii::app()->user->checkAccess('scheduler'))
		{
			// reset in_charge_id - not allowed to change
			$this->in_charge_id = $this->getOldAttributeValue('in_charge_id');
			
			// not allowed to change description of day or crew
			switch($this->level)
			{
				case Planning::planningLevelCrewInt :
				case Planning::planningLevelDayInt :
					// reset name - not allowed to change
					$this->name = $this->getOldAttributeValue('name');
			}
		}

		return parent::beforeSave();
	}

/*	// ensure that where possible a pk has been passed from parent
	public function assertFromParent()
	{
		// assert from parent won't work as normal from within planning because the parents are all internal in the nested set
		// however we do need to ensure that project_id, and subsequently that client_id are set
		$project = Project::model()->findByPk($_SESSION['Planning']['value']);
		$_SESSION['Client']['name'] = 'id';
		$_SESSION['Client']['value'] = $project->projectType->client_id;
		
		return;
	}*/	
	
}

?>