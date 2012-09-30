<?php

/**
 * This is the model class for table "crew".
 *
 * The followings are the available columns in table 'crew':
 * @property string $id
 * @property string $level
 * @property string $day_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Planning $id0
 * @property CrewLevel $level0
 * @property Staff $staff
 * @property Day $day
 * @property Task[] $tasks
 */
class Crew extends ActiveRecord
{
	public $searchInCharge;
	public $in_charge_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'crew';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('day_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('id, level, day_id, in_charge_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, searchInCharge, day_id, staff_id', 'safe', 'on'=>'search'),
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
			'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
			'level0' => array(self::BELONGS_TO, 'CrewLevel', 'level'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'day' => array(self::BELONGS_TO, 'Day', 'day_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'crew_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'Crew',
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
		);
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
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.day_id',$this->day_id);
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);

		// join
		$criteria->with = array(
			'id0',
			'id0.inCharge',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'in_charge_id');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id0->inCharge->first_name';
		$displaAttr[]='id0->inCharge->last_name';

		return $displaAttr;
	}

	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(!(Yii::app()->controller->id == 'planning'))
		{
			return parent::assertFromParent();
		}
	}

	public function beforeSave() {
		// ensure that only scheduler is able to alter the in_charge
		if(!Yii::app()->user->checkAccess('scheduler'))
		{
			$this->in_charge_id = $this->getOldAttributeValue('in_charge_id');
		}
		
		return parent::beforeSave();
	}

}

?>