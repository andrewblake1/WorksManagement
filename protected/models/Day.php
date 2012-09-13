<?php

/**
 * This is the model class for table "day".
 *
 * The followings are the available columns in table 'day':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Crew[] $crews
 * @property Schedule $id0
 * @property DayLevel $level0
 * @property Staff $staff
 * @property Project $project
 */
class Day extends ActiveRecord
{
	public $searchInCharge;
	public $name;
	public $in_charge_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'day';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('id, level, project_id, in_charge_id', 'length', 'max'=>10),
			array('name', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, level, searchInCharge, project_id, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		// class name for the relations automatically generated below.
		return array(
			'crews' => array(self::HAS_MANY, 'Crew', 'day_id'),
			'id0' => array(self::BELONGS_TO, 'Schedule', 'id'),
			'level0' => array(self::BELONGS_TO, 'DayLevel', 'level'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'Day',
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
			'name' => 'Day',
		);
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			'id0.name AS name',
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.project_id',$this->project_id);
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
		$columns[] = 'name';
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'in_charge_id');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge', 'name');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id0->name';

		return $displaAttr;
	}

	public function afterFind() {
		$this->name = $this->id0->name;
		
		parent::afterFind();
	}

}

?>