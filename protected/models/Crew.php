<?php

/**
 * This is the model class for table "crew".
 *
 * The followings are the available columns in table 'crew':
 * @property string $id
 * @property string $preferred_date
 * @property string $earliest_date
 * @property string $date_scheduled
 * @property integer $in_charge_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $inCharge
 * @property Staff $staff
 * @property Task[] $tasks
 */
class Crew extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchInCharge;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Crew the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

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
			array('in_charge_id, staff_id', 'required'),
			array('in_charge_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('preferred_date, earliest_date, date_scheduled', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, preferred_date, earliest_date, date_scheduled, searchInCharge, searchStaff', 'safe', 'on'=>'search'),
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
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'crew_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Crew',
			'crew' => 'In Charge/Date Scheduled (First/Last/Email/Scheduled',
			'preferred_date' => 'Preferred Date',
			'earliest_date' => 'Earliest Date',
			'date_scheduled' => 'Date Scheduled',
			'in_charge_id' => 'In Charge (First/Last/Email)',
			'searchInCharge' => 'In Charge (First/Last/Email)',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('preferred_date',$this->preferred_date,true);
		$criteria->compare('earliest_date',$this->earliest_date,true);
		$criteria->compare('date_scheduled',$this->date_scheduled,true);
		$this->compositeCriteria($criteria, array('inCharge.first_name','inCharge.last_name','inCharge.email'), $this->searchInCharge);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array('staff','inCharge');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'preferred_date',
			'earliest_date',
			'date_scheduled',
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}


	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'inCharge'=>array('first_name', 'last_name', 'email'),
			'date_scheduled'
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge');
	}

}