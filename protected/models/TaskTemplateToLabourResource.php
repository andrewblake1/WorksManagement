<?php

/**
 * This is the model class for table "tbl_task_template_to_labour_resource".
 *
 * The followings are the available columns in table 'tbl_task_template_to_labour_resource':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $labour_resource_id
 * @property integer $labour_resource_to_supplier_id
 * @property string $level
 * @property integer $mode_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property LabourResourceToSupplier $labourResource
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level0
 * @property LabourResourceToSupplier $labourResourceToSupplier
 * @property TaskTemplateToMutuallyExclusiveRole[] $taskTemplateToMutuallyExclusiveRoles
 * @property TaskTemplateToMutuallyExclusiveRole[] $taskTemplateToMutuallyExclusiveRoles1
 * @property TaskTemplateToMutuallyExclusiveRole[] $taskTemplateToMutuallyExclusiveRoles2
 */
class TaskTemplateToLabourResource extends ActiveRecord
{
	public $searchLabourResource;
	public $searchLevel;
	public $searchMode;
	public $searchSupplier;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'labourResource' => array(self::BELONGS_TO, 'LabourResource', 'labour_resource_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'labourResourceToSupplier' => array(self::BELONGS_TO, 'LabourResourceToSupplier', 'labour_resource_to_supplier_id'),
            'taskTemplateToMutuallyExclusiveRoles' => array(self::HAS_MANY, 'TaskTemplateToMutuallyExclusiveRole', 'task_template_id'),
            'taskTemplateToMutuallyExclusiveRoles1' => array(self::HAS_MANY, 'TaskTemplateToMutuallyExclusiveRole', 'parent_id'),
            'taskTemplateToMutuallyExclusiveRoles2' => array(self::HAS_MANY, 'TaskTemplateToMutuallyExclusiveRole', 'child_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);

		// with
		$criteria->with = array(
			'labourResource',
			'level0',
			'mode',
			'labourResourceToSupplier.supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchLabourResource', 'LabourResource', 'labour_resource_id');
 		$columns[] = 'quantity';
		$columns[] = 'duration';
 		$columns[] = 'searchMode';
 		$columns[] = 'searchLevel';
 		$columns[] = 'searchSupplier';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchLabourResource',
			'searchMode',
			'searchLevel',
			'searchSupplier',
		);
	}

	public function scopeTaskTemplate($exclude_id, $task_template_id, $mode_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('t.task_template_id', $task_template_id);
		$criteria->compare('t.mode_id', $mode_id);
		$criteria->addNotInCondition('t.id', array($exclude_id));

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
}