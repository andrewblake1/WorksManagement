<?php

/**
 * This is the model class for table "project_to_AuthAssignment".
 *
 * The followings are the available columns in table 'project_to_AuthAssignment':
 * @property string $id
 * @property string $project_id
 * @property integer $AuthAssignment_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Project $project
 * @property AuthAssignment $authAssignment
 * @property Staff $staff
 */
class ProjectToAuthAssignment extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProject;
	public $searchAuthAssignment;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToAuthAssignment the static model class
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
		return 'project_to_AuthAssignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, AuthAssignment_id, staff_id', 'required'),
			array('AuthAssignment_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchProject, searchAuthAssignment, searchStaff', 'safe', 'on'=>'search'),
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
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'authAssignment' => array(self::BELONGS_TO, 'AuthAssignment', 'AuthAssignment_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project To Auth Assignment',
			'project_id' => 'Project',
			'searchProject' => 'Project',
			'AuthAssignment_id' => 'Auth Assignment (Role/First/Last/Email)',
			'searchAuthAssignment' => 'Auth Assignment (Role/First/Last/Email)',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('project.id',$this->searchProject,true);
		$this->compositeCriteria(
			$criteria,
			array(
				'authAssignment.itemname',
				'authAssignment.user.first_name',
				'authAssignment.user.last_name',
				'authAssignment.user.email'
			),
			$this->searchAuthAssignment
		);
		
		$criteria->with = array('authAssignment.itemname','authAssignment.user','project');

		$delimiter = Yii::app()->params['delimiter']['search'];
		$criteria->select=array(
			'id',
			'project.id AS searchProject',
			"CONCAT_WS('$delimiter',
				authAssignment.itemname,
				authAssignment.user.first_name,
				authAssignment.user.last_name,
				authAssignment.user.email
				) AS searchAuthAssignment",
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProject', 'searchAuthAssignment');
	}
}