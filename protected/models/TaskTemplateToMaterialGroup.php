<?php

/**
 * This is the model class for table "tbl_task_template_to_material_group".
 *
 * The followings are the available columns in table 'tbl_task_template_to_material_group':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $standard_id
 * @property integer $material_group_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property string $selection_tooltip
 * @property string $comment
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property MaterialGroup $materialGroup
 * @property MaterialGroup $standard
 * @property TaskTemplate $taskTemplate
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups1
 */
class TaskTemplateToMaterialGroup extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialGroup;
	
	public $standard_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(), array(
			array('standard_id', 'required'),
			array('standard_id, quantity', 'numerical', 'integerOnly'=>true),
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
            'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
            'standard' => array(self::BELONGS_TO, 'MaterialGroup', 'standard_id'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'material_group_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups1' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'task_template_to_material_group_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchMaterialGroup', $this->searchMaterialGroup, 'materialGroup.description', true);
		
		$criteria->with = array(
			'materialGroup',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchMaterialGroup';
 		$columns[] = 'quantity';
 		$columns[] = 'comment';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
 		$columns[] = 'quantity_tooltip';
 		$columns[] = 'selection_tooltip';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchMaterialGroup',
			'comment',
		);
	}
	
	public function beforeValidate()
	{
		$materialGroup = MaterialGroup::model()->findByPk($this->material_group_id);
		$this->standard_id = $materialGroup->standard_id;
		
		return parent::beforeValidate();
	}
	
	public function afterFind() {
		$materialGroup = MaterialGroup::model()->findByPk($this->material_group_id);
		$this->standard_id = $materialGroup->standard_id;
		
		parent::afterFind();
	}

}