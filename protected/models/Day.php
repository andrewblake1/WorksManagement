<?php

/**
 * This is the model class for table "tbl_day".
 *
 * The followings are the available columns in table 'tbl_day':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property string $scheduled
 * @property integer $preferred_mon
 * @property integer $preferred_tue
 * @property integer $preferred_wed
 * @property integer $preferred_thu
 * @property integer $preferred_fri
 * @property integer $preferred_sat
 * @property integer $preferred_sun
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Crew[] $crews
 * @property User $updatedBy
 * @property Planning $level
 * @property Project $project
 * @property Planning $id0
 */
class Day extends ActiveRecord
{
	static $niceNamePlural = 'Days';
	public $searchInCharge;
	public $searchName;
	public $name;
	public $in_charge_id;
	/**
	 * inline checkbox property 
	 */
	public $preferred = array();

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('in_charge_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('preferred', 'safe'),
		));
	}

	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'crews' => array(self::HAS_MANY, 'Crew', 'day_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'level' => array(self::BELONGS_TO, 'Planning', 'level'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'preferred_mon' => 'Mo',
			'preferred_tue' => 'Tu',
			'preferred_wed' => 'We',
			'preferred_thu' => 'Th',
			'preferred_fri' => 'Fr',
			'preferred_sat' => 'Sa',
			'preferred_sun' => 'Su',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		// select
		$criteria->compareAs('searchName', $this->searchName, 'id0.name', true);
		$criteria->composite('searchInCharge', $this->searchInCharge, array(
			'contact.first_name',
			'contact.last_name',
			'contact.email',)
			);
//		$criteria->compare('t.scheduled',Yii::app()->format->toMysqlDate($this->scheduled));

		// with
		$criteria->with = array(
			'id0',
			'id0.inCharge.contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
 		$columns[] = 'scheduled:date';
		$columns[] = static::linkColumn('searchInCharge', 'User', 'in_charge_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='scheduled';

		return $displaAttr;
	}

	public function afterFind() {
		// prepare check box row items
		if($this->preferred_mon)
		{
			$this->preferred[] = 0;
		}
		if($this->preferred_tue)
		{
			$this->preferred[] = 1;
		}
		if($this->preferred_wed)
		{
			$this->preferred[] = 2;
		}
		if($this->preferred_thu)
		{
			$this->preferred[] = 3;
		}
		if($this->preferred_fri)
		{
			$this->preferred[] = 4;
		}
		if($this->preferred_sat)
		{
			$this->preferred[] = 5;
		}
		if($this->preferred_sun)
		{
			$this->preferred[] = 6;
		}

		$this->name = $this->id0->name;
	
		parent::afterFind();
	}

	public function assertFromParent($modelName = NULL)
	{
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

		// ensure no editing unless scehduler
		if(!Yii::app()->user->checkAccess('scheduler'))
		{
			// reset
			$this->scheduled = $this->getOldAttributeValue('scheduled');
			$this->name = $this->getOldAttributeValue('name');
		}
			
		if(!empty($this->preferred))
		{
			$this->preferred_mon = in_array('0', $this->preferred);
			$this->preferred_tue = in_array('1', $this->preferred);
			$this->preferred_wed = in_array('2', $this->preferred);
			$this->preferred_thu = in_array('3', $this->preferred);
			$this->preferred_fri = in_array('4', $this->preferred);
			$this->preferred_sat = in_array('5', $this->preferred);
			$this->preferred_sun = in_array('6', $this->preferred);
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
		$planning->name = $this->name;
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
	public function createSave(&$models=array(), $runValidation = true)
	{
		// need to insert a row into the planning nested set model so that the id can be used here
		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];

		if($saved = $planning->appendTo(Planning::model()->findByPk($this->project_id)))
		{
			$this->id = $planning->id;
			$saved = parent::createSave($models);

			// add a Crew
			$crew = new Crew;
			$crew->day_id = $this->id;
			$saved = $crew->createSave($models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved;
	}

}

?>