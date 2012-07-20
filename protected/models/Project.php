<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
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
			array('travel_time_1_way, critical_completion, planned', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, travel_time_1_way, critical_completion, planned, client_id, staff_id', 'safe', 'on'=>'search'),
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
		return array(
			'id' => 'ID',
			'travel_time_1_way' => 'Travel Time 1 Way',
			'critical_completion' => 'Critical Completion',
			'planned' => 'Planned',
			'client_id' => 'Client',
			'staff_id' => 'Staff',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('travel_time_1_way',$this->travel_time_1_way,true);
		$criteria->compare('critical_completion',$this->critical_completion,true);
		$criteria->compare('planned',$this->planned,true);
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}