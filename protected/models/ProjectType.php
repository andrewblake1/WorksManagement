<?php

/**
 * This is the model class for table "project_type".
 *
 * The followings are the available columns in table 'project_type':
 * @property integer $id
 * @property string $description
 * @property integer $client_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericProjectType[] $genericProjectTypes
 * @property Project[] $projects
 * @property Staff $staff
 * @property Client $client
 * @property ProjectTypeToAuthItem[] $projectTypeToAuthItems
 * @property TaskType[] $taskTypes 
 */
class ProjectType extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, client_id, staff_id', 'required'),
			array('client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, client_id, deleted, searchClient, searchStaff', 'safe', 'on'=>'search'),
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
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'project_type_id'),
			'projects' => array(self::HAS_MANY, 'Project', 'project_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'projectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectTypeToAuthItem', 'project_type_id'),
			'taskTypes' => array(self::HAS_MANY, 'TaskType', 'project_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project type',
			'client_id' => 'Client',
			'searchClient' => 'Client',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.description',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.client_id', $this->client_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		
		return $columns;
	}

}

?>