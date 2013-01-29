<?php

/**
 * This is the model class for table "project_to_project_type_to_AuthItem".
 *
 * The followings are the available columns in table 'project_to_project_type_to_AuthItem':
 * @property string $id
 * @property string $project_id
 * @property integer $project_type_to_AuthItem_id
 * @property integer $AuthAssignment_id
 * @property string $itemname
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Project $project
 * @property ProjectTypeToAuthItem $projectTypeToAuthItem
 * @property AuthAssignment $itemname0
 * @property AuthAssignment $authAssignment
 * @property Staff $staff
 */
class ProjectToProjectTypeToAuthItem extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProject;
	public $searchProjectTypeToAuthItem;
	public $searchAuthAssignment;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_to_project_type_to_AuthItem';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, project_type_to_AuthItem_id, AuthAssignment_id, itemname, staff_id', 'required'),
			array('project_type_to_AuthItem_id, AuthAssignment_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			array('itemname', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchProject, searchProjectTypeToAuthItem, searchAuthAssignment, id, project_id, project_type_to_AuthItem_id, AuthAssignment_id, itemname, staff_id', 'safe', 'on'=>'search'),
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
			'projectTypeToAuthItem' => array(self::BELONGS_TO, 'ProjectTypeToAuthItem', 'project_type_to_AuthItem_id'),
			'itemname0' => array(self::BELONGS_TO, 'AuthAssignment', 'itemname'),
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
			'project_id' => 'Client/Project',
			'searchProject' => 'Client/Project',
			'project_type_to_AuthItem_id' => 'Project type',
			'searchProjectTypeToAuthItem' => 'Project type',
			'AuthAssignment_id' => 'First/Last/Email',
			'searchAuthAssignment' => 'First/Last/Email',
			'itemname' => 'Role',
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
			't.id',	// needed for delete and update buttons
			't.AuthAssignment_id',
			"CONCAT_WS('$delimiter',
				user.first_name,
				user.last_name,
				user.email
				) AS searchAuthAssignment",
			't.itemname',
		);
		
		// where
		$criteria->compare('AuthAssignment_id',$this->AuthAssignment_id);
		$this->compositeCriteria($criteria,
			array(
				'user.first_name',
				'user.last_name',
				'user.email',
			),
			$this->searchAuthAssignment
		);
		$criteria->compare('itemname',$this->itemname,true);
		$criteria->compare('t.project_id',$this->project_id);
		
		// join
		$criteria->with = array(
			'authAssignment.user',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('itemname');
        $columns[] = static::linkColumn('searchAuthAssignment', 'AuthAssignment', 'AuthAssignment_id');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAuthAssignment');
	}

	public function beforeValidate()
	{
		// if user has chosen an auth assigment
		if(isset($this->AuthAssignment_id))
		{
			// set the remaining required fields
			$modelAuthAssignment = AuthAssignment::model()->findByPk($this->AuthAssignment_id);
			$this->itemname = $modelAuthAssignment->itemname;
			$modelProject = Project::model()->findByPk($this->project_id);
			$modelProject->attributes;
			$modelProjectTypeToAuthItem = ProjectTypeToAuthItem::model()->findByAttributes(array(
				'project_type_id'=>$modelProject->project_type_id, 
				'AuthItem_name'=>$this->itemname,
			));
			$this->project_type_to_AuthItem_id = $modelProjectTypeToAuthItem->id;
		}
		return parent::beforeValidate();
	}
}

?>