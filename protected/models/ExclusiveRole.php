<?php

/**
 * This is the model class for table "tbl_exclusive_role".
 *
 * The followings are the available columns in table 'tbl_exclusive_role':
 * @property string $id
 * @property string $parent_id
 * @property string $child_id
 * @property string $planning_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property RoleData $planning
 * @property RoleData $parent
 * @property RoleData $child
 */
class ExclusiveRole extends ActiveRecord
{
	public $searchExclusiveTo;
	public $task_to_role_id;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('task_to_role_id', 'numerical', 'integerOnly'=>true),
			array('task_to_role_id', 'safe'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'planning' => array(self::BELONGS_TO, 'RoleData', 'planning_id'),
			'parent' => array(self::BELONGS_TO, 'RoleData', 'parent_id'),
			'child' => array(self::BELONGS_TO, 'RoleData', 'child_id'),
		);
	}
   public function attributeLabels()
    {
        return array(
            'child_id' => 'Exclusive to',
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		// a slight difference here due to the schema where parent isn't actually task_to_role
		$taskToRole = TaskToRole::model()->findByPk($this->task_to_role_id);
		$this->parent_id = $taskToRole->role_data_id;
		
		$criteria->compareAs('searchExclusiveTo', $this->searchExclusiveTo, 'humanResource.auth_item_name', true);

		$criteria->with = array(
			'child.humanResource',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchExclusiveTo';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchExclusiveTo',
		);
	}
 
	public function beforeValidate()
	{
		// a slight difference here due to the schema where parent isn't actually task_to_role
		$taskToRole = TaskToRole::model()->findByPk($this->task_to_role_id);
		$this->parent_id = $taskToRole->role_data_id;
		$this->planning_id = $this->parent->planning_id;
		return parent::beforeValidate();
	}

}