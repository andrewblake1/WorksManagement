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
 */
class ProjectTemplateToAuthItem extends ActiveRecord
{
	public $searchAuthItem;	
	
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	public function scopeTaskTemplateToAction($task_template_id)
	{
//TODO this and another location that restrict lists need to include the current record if updating rather than excluding it
// also add deleted to unique indexes and make deleted += 1 so not violating constraints when adding new and removing old multiple times
		$criteria=new DbCriteria;
		$criteria->compare('taskTemplate.id',$task_template_id);
		$criteria->compare('taskTemplate.deleted',0);
		$criteria->join='
			JOIN tbl_task_template taskTemplate
			USING ( project_template_id )
			';
		$criteria->distinct=true;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
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
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'searchAuthItem' => 'Role',			
			'auth_item_name' => 'Role',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAuthItem', $this->searchAuthItem,'authItemName.description',true);
		
		// join
		$criteria->with = array(
			'authItemName',
		);

		return $criteria;
	}
	
	public function getAdminColumns()
	{
        $columns[] = 'searchAuthItem';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='searchAuthItem';

		return $displaAttr;
	}
	
	
	public static function add($authItemName, $projectId, &$models = array())
	{
		$projectToAuthItem = new ProjectToAuthItem;
		
		$projectToAuthItem->auth_item_name = $authItemName;
		$projectToAuthItem->project_id = $projectId;

		return $projectToAuthItem->createSave($models);
	}

}