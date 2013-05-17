<?php

/**
 * This is the model class for table "tbl_project_to_project_template_to_auth_item".
 *
 * The followings are the available columns in table 'tbl_project_to_project_template_to_auth_item':
 * @property string $id
 * @property string $project_id
 * @property integer $project_template_to_auth_item_id
 * @property integer $auth_assignment_id
 * @property string $item_name
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Project $project
 * @property ProjectTemplateToAuthItem $projectTemplateToAuthItem
 * @property AuthAssignment $itemName
 * @property AuthAssignment $authAssignment
 * @property User $updatedBy
 */
class ProjectToProjectTemplateToAuthItem extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProject;
	public $searchProjectTemplateToAuthItem;
	public $searchAuthAssignment;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';

	protected $defaultSort = array('t.item_name');
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, project_template_to_auth_item_id, auth_assignment_id, item_name', 'required'),
			array('project_template_to_auth_item_id, auth_assignment_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			array('item_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchProject, searchProjectTemplateToAuthItem, searchAuthAssignment, id, project_id, project_template_to_auth_item_id, auth_assignment_id, item_name', 'safe', 'on'=>'search'),
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
            'projectTemplateToAuthItem' => array(self::BELONGS_TO, 'ProjectTemplateToAuthItem', 'project_template_to_auth_item_id'),
            'itemName' => array(self::BELONGS_TO, 'AuthAssignment', 'item_name'),
            'authAssignment' => array(self::BELONGS_TO, 'AuthAssignment', 'auth_assignment_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
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
			'project_template_to_auth_item_id' => 'Project type',
			'searchProjectTemplateToAuthItem' => 'Project type',
			'auth_assignment_id' => 'First/Last/Email',
			'searchAuthAssignment' => 'First/Last/Email',
			'item_name' => 'Role',
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
			't.auth_assignment_id',
			"CONCAT_WS('$delimiter',
				user.first_name,
				user.last_name,
				user.email
				) AS searchAuthAssignment",
			't.item_name',
		);
		
		// where
		$criteria->compare('auth_assignment_id',$this->auth_assignment_id);
		$this->compositeCriteria($criteria,
			array(
				'user.first_name',
				'user.last_name',
				'user.email',
			),
			$this->searchAuthAssignment
		);
		$criteria->compare('item_name',$this->item_name,true);
		$criteria->compare('t.project_id',$this->project_id);
		
		// with
		$criteria->with = array(
			'authAssignment.user',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('item_name');
        $columns[] = static::linkColumn('searchAuthAssignment', 'AuthAssignment', 'auth_assignment_id');
		
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
		if(isset($this->auth_assignment_id))
		{
			// set the remaining required fields
			$modelAuthAssignment = AuthAssignment::model()->findByPk($this->auth_assignment_id);
			$this->item_name = $modelAuthAssignment->item_name;
			$modelProject = Project::model()->findByPk($this->project_id);
			$modelProject->attributes;
			$modelProjectTemplateToAuthItem = ProjectTemplateToAuthItem::model()->findByAttributes(array(
				'project_template_id'=>$modelProject->project_template_id, 
				'auth_item_name'=>$this->item_name,
			));
			$this->project_template_to_auth_item_id = $modelProjectTemplateToAuthItem->id;
		}
		return parent::beforeValidate();
	}
}

?>