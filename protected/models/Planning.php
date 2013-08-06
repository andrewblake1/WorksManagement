<?php

/**
 * This is the model class for table "tbl_planning".
 *
 * The followings are the available columns in table 'tbl_planning':
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property string $level
 * @property string $name
 * @property integer $in_charge_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Crew $crew
 * @property Day[] $days
 * @property Day $day
 * @property DutyData[] $dutyDatas
 * @property DutyData[] $dutyDatas1
 * @property User $updatedBy
 * @property User $inCharge
 * @property Level $level0
 * @property Project[] $projects
 * @property Project $project
 * @property LabourResourceData[] $labourResourceDatas
 * @property LabourResourceData[] $labourResourceDatas1
 * @property Task[] $tasks
 * @property Task $task
 */
class Planning extends CategoryActiveRecord {
	public $levelName;

	static $niceNamePlural = 'Planning';
	
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
		$rules = parent::rules();
		$rules[] = array('project_id', 'numerical', 'integerOnly'=>true);
		
		return $rules;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'crew' => array(self::HAS_ONE, 'Crew', 'id'),
            'day' => array(self::HAS_ONE, 'Day', 'id'),
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'planning_id'),
            'dutyDatas1' => array(self::HAS_MANY, 'DutyData', 'level'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'inCharge' => array(self::BELONGS_TO, 'User', 'in_charge_id'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'project' => array(self::HAS_ONE, 'Project', 'id'),
            'resourceDatas' => array(self::HAS_MANY, 'LabourResourceData', 'planning_id'),
            'resourceDatas1' => array(self::HAS_MANY, 'LabourResourceData', 'level'),
            'task' => array(self::HAS_ONE, 'Task', 'id'),
        );
    }


// TODO: either strip this to database trigger or ideally remove the need for day->task_id and crew->day_id completely i.e. run off
// ajaxtree completely. It is possible for ajax tree not to match these parents hence needs fixing.
// another alternative is to standard parent_id in the planning table on afterSave but at time of writing not sure of effect on surroundings
	public function afterSave() {
		
		// need to ensure that the parent id stays up to date the task and crew tables. task->crew_id and crew->day_id
		// this is because it is possible to rearrange in the ajax tree
		// NB: project and day parent id's can't be mucked up in ajax tree (client_id and project_id respectively)
		switch($this->level)
		{
			case self::planningLevelCrewInt :
				if($crew=Crew::model()->findByPk($this->id))
				{
					$crew->day_id = $this->parent->id;
					$crew->save();
				}
				break;
			case self::planningLevelTaskInt :
				if($task=Task::model()->findByPk($this->id))
				{
					$task->crew_id = $this->parent->id;
					$task->save();
				}
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
			$dayCounter = 0;
			switch($category->level)
			{
				case Planning::planningLevelDayInt :
					$day = Day::model()->findByPk($category->id);
					$label = "D{$category->id} ";
					$label .= empty($day->scheduled) ? '' : $day->scheduled;
					$crewCounter = 0;
					break;
				case Planning::planningLevelCrewInt :
					$crew =Crew::model()->findByPk($category->id);
					$label = "C{$category->id} ";
					$label .= empty($category->in_charge_id) ? '' : Crew::getNiceName(null, $crew);//					$label .= empty($category->in_charge_id) ? "Crew $crewCounter" : Crew::getNiceName(null, $crew);
					break;
				case Planning::planningLevelTaskInt :
					$label = "T{$category->id} ";
					$label .= $category->name;
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
			case self::planningLevelProjectInt :
				$this->levelName = self::planningLevelProject;
				break;
			case self::planningLevelDayInt :
				$this->levelName = self::planningLevelDay;
				break;
			case self::planningLevelCrewInt :
				$this->levelName = self::planningLevelCrew;
				break;
			case self::planningLevelTaskInt :
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

	public function assertFromParent() {
		
		$project = Project::model()->findByPk($this->project_id);
		Controller::setUpdateId($this->project_id, 'Project');
		return $project->assertFromParent();
	}
	
	/**
	 * This needs overriding in order to cope with code assuming parent is project
	 * @param type $attributes
	 * @param type $condition
	 * @param type $params
	 */
	public function countByAttributes($attributes, $condition = '', $params = array()) {
		if(isset($attributes['project_id']))
		{
			$attributes['id'] = $attributes['project_id'];
			unset($attributes['project_id']);
		}
		
		return parent::countByAttributes($attributes, $condition, $params);
	}
}

?>