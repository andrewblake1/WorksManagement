<?php

/**
 * This is the model class for table "tbl_crew".
 *
 * The followings are the available columns in table 'tbl_crew':
 * @property string $id
 * @property string $level
 * @property string $day_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Planning $id0
 * @property CrewLevel $level0
 * @property User $updatedBy
 * @property Day $day
 * @property Task[] $tasks
 */
class Crew extends ActiveRecord
{
	public $searchInCharge;
	public $in_charge_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('day_id', 'required'),
			array('id, level, day_id, in_charge_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, level, searchInCharge, day_id', 'safe', 'on'=>'search'),
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
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
            'level0' => array(self::BELONGS_TO, 'CrewLevel', 'level'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'day' => array(self::BELONGS_TO, 'Day', 'day_id'),
            'tasks' => array(self::HAS_MANY, 'Task', 'crew_id'),
        );
    }

	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			"CONCAT_WS('$delimiter',
				contact.first_name,
				contact.last_name,
				contact.email
				) AS searchInCharge",
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.day_id',$this->day_id);
		$this->compositeCriteria($criteria,
			array(
				'contact.first_name',
				'contact.last_name',
				'contact.email',
			),
			$this->searchInCharge
		);

		// with
		$criteria->with = array(
			'id0',
			'id0.inCharge.contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('id');
        $columns[] = static::linkColumn('searchInCharge', 'User', 'in_charge_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id0->inCharge->contact->first_name';
		$displaAttr[]='id0->inCharge->contact->last_name';

		return $displaAttr;
	}

	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{
// TODO: repeated in task, an day
		// if update in planning view
		if(isset($_POST['controller']['Planning']) && isset($_GET['project_id']))
		{
			// ensure that that at least the parents primary key is set for the admin view of planning
			Controller::setAdminParam('project_id', $_GET['project_id'], 'Planning');
		}
		
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(!(Yii::app()->controller->id == 'planning'))
		{
			return parent::assertFromParent();
		}
	}

	public function beforeSave() {
		// ensure that only scheduler is able to alter the inCharge
		if(!Yii::app()->user->checkAccess('scheduler'))
		{
			$this->in_charge_id = $this->getOldAttributeValue('in_charge_id');
		}
		
		return parent::beforeSave();
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		// get the planning model
		$planning = Planning::model()->findByPk($this->id);
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		// need to insert a row into the planning nested set model so that the id can be used here
		
		// create a root node
		$planning = new Planning;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		if($saved = $planning->appendTo(Planning::model()->findByPk($this->day_id)))
		{
			$this->id = $planning->id;
			$saved = parent::createSave($models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved;
	}

}

?>