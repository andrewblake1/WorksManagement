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
 * @property TaskTemplateToHumanResource $taskTemplate
 * @property User $updatedBy
 * @property TaskTemplateToHumanResource $parent
 * @property TaskTemplateToHumanResource $child
 */
class TaskTemplateToExclusiveRole extends ActiveRecord
{
	public $searchExclusiveTo;
	public $task_template_to_human_resource_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('task_template_to_human_resource_id', 'numerical', 'integerOnly'=>true),
			array('task_template_to_human_resource_id', 'safe'),
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
			'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplateToHumanResource', 'task_template_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'parent' => array(self::BELONGS_TO, 'TaskTemplateToHumanResource', 'parent_id'),
			'child' => array(self::BELONGS_TO, 'TaskTemplateToHumanResource', 'child_id'),
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

		// a slight difference here due to the schema where parent isn't actually task_to_human_resource
		$taskTemplateToHumanResource = TaskTemplateToHumanResource::model()->findByPk($this->task_template_to_human_resource_id);
		$this->parent_id = $taskTemplateToHumanResource->id;
		
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
		// a slight difference here due to the schema where parent isn't actually task_to_human_resource
		$taskTemplateToHumanResource = TaskTemplateToHumanResource::model()->findByPk($this->task_template_to_human_resource_id);
		$this->parent_id = $taskTemplateToHumanResource->id;
		$this->task_template_id = $this->parent->task_template_id;
		return parent::beforeValidate();
	}

}