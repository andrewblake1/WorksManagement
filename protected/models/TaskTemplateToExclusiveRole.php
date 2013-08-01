<?php

/**
 * This is the model class for table "tbl_task_template_to_exclusive_role".
 *
 * The followings are the available columns in table 'tbl_task_template_to_exclusive_role':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $parent_id
 * @property integer $child_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplateToRole $taskTemplate
 * @property User $updatedBy
 * @property TaskTemplateToRole $parent
 * @property TaskTemplateToRole $child
 */
class TaskTemplateToExclusiveRole extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplateToRole', 'task_template_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'parent' => array(self::BELONGS_TO, 'TaskTemplateToRole', 'parent_id'),
			'child' => array(self::BELONGS_TO, 'TaskTemplateToRole', 'child_id'),
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

		$criteria->compareAs('searchExclusiveTo', $this->searchExclusiveTo, 'child.auth_item_name', true);

		$criteria->with = array(
			'childDutyStep',
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
 
	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('TaskTemplateToRole'=>'parent_id'));
	}	
	
	public function beforeValidate()
	{
		$this->task_template_id = $this->parent->task_template_id;
		parent::beforeValidate();
	}

}