<?php

/**
 * This is the model class for table "tbl_project_template_to_auth_item".
 *
 * The followings are the available columns in table 'tbl_project_template_to_auth_item':
 * @property integer $id
 * @property integer $project_template_id
 * @property string $auth_item_name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ProjectTemplate $projectTemplate
 * @property AuthItem $authItemName
 * @property User $updatedBy
 * @property ProjectToProjectTemplateToAuthItem[] $projectToProjectTemplateToAuthItems
 * @property TaskTemplateToDutyType[] $taskTemplateToDutyTypes
 * @property TaskTemplateToDutyType[] $taskTemplateToDutyTypes1
 */
class ProjectTemplateToAuthItem extends ActiveRecord
{
	public $searchAuthItem;	
	
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	public function scopeTaskTemplateToDutyType($task_template_id)
	{
//TODO this and another location that restrict lists need to include the current record if updating rather than excluding it
// also add deleted to unique indexes and make deleted += 1 so not violating constraints when adding new and removing old multiple times
		$criteria=new DbCriteria;
		$criteria->compare('taskTemplate.id',$task_template_id);
		$criteria->join='
			JOIN tbl_task_template taskTemplate
			USING ( project_template_id )
			';
		$criteria->distinct=true;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_template_id, auth_item_name', 'required'),
			array('project_template_id', 'numerical', 'integerOnly'=>true),
			array('auth_item_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchAuthItem, project_template_id, auth_item_name', 'safe', 'on'=>'search'),
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
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectToProjectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTemplateToAuthItem', 'project_template_to_auth_item_id'),
            'taskTemplateToDutyTypes' => array(self::HAS_MANY, 'TaskTemplateToDutyType', 'project_template_id'),
            'taskTemplateToDutyTypes1' => array(self::HAS_MANY, 'TaskTemplateToDutyType', 'project_template_to_auth_item_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project type to role',
			'project_template_id' => 'Project type',
			'searchAuthItem' => 'Role',			
			'auth_item_name' => 'Role',
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
			't.id',	// needed for delete and update buttons
			'authItemName.description AS searchAuthItem',
		);

		// where
		$criteria->compare('authItemName.description',$this->searchAuthItem,true);
		$criteria->compare('t.project_template_id',$this->project_template_id);
		
		// join
		$criteria->with = array(
			'authItemName',
		);

		return $criteria;
	}
	
	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('searchAuthItem');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='authItemName->description';

		return $displaAttr;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAuthItem');
	}

}