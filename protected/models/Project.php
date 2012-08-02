<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
 * @property string $description
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $client_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Client $client
 * @property Staff $staff
 * @property ProjectToAuthAssignment[] $projectToAuthAssignments
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property Task[] $tasks
 */
class Project extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchClient;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
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
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, staff_id', 'required'),
			array('client_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('travel_time_1_way, critical_completion, planned', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, travel_time_1_way, critical_completion, planned, searchClient, searchStaff', 'safe', 'on'=>'search'),
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
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthAssignment', 'project_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'project_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project',
			'travel_time_1_way' => 'Travel Time 1 Way',
			'critical_completion' => 'Critical Completion',
			'planned' => 'Planned',
			'client_id' => 'Client',
			'searchClient' => 'Client',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('travel_time_1_way',$this->travel_time_1_way,true);
		$criteria->compare('critical_completion',$this->critical_completion,true);
		$criteria->compare('planned',$this->planned,true);
		$criteria->compare('client.name',$this->searchClient);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array('staff','client');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'description',
			'travel_time_1_way',
			'critical_completion',
			'planned',
			'client.name AS searchClient',
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchClient');
	}
}