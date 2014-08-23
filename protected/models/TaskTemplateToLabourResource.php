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
 * @property Level $level
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
	public $searchSupplierId;
	public $durationTemp;	// used to get around an awkward validation situation where want duration to be required if Primary role but not if Secondary role or type not set
	public $type;	// role type ie. Primary role or Secondary role

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(array('type')), array(
			array('durationTemp', 'required'),
			array('type', 'safe'),
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
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'labourResource' => array(self::BELONGS_TO, 'LabourResource', 'labour_resource_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level' => array(self::BELONGS_TO, 'Level', 'level'),
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

		$criteria->select=array(
			't.*',
			'supplier.id AS searchSupplierId',
		);

		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);

		// with
		$criteria->with = array(
			'labourResource',
			'level',
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
        $columns[] = static::linkColumn('searchSupplier', 'Supplier', 'searchSupplierId');
		
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
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'durationTemp' => 'Duration',
			'searchPrimarySecondary' => 'Type',
			'searchLabourResource' => 'Role',
			'labour_resource_id' => 'Role',
		));
	}
	
	public function afterFind() {
		parent::afterFind();

		$this->durationTemp = $this->duration;
	}
	
	public function beforeValidate()
	{
		// if primary role then all values can be inserted, if secondary then we want to clear
		// start, duration, estimated duration, and supplier for data item and children
		if($this->type == 'Secondary')
		{
			$this->duration = null;
		}
		else
		{
			$this->duration = $this->durationTemp;
		}

		// a hack to get around not easily being able to adjust rules
		$this->durationTemp = 0;
		
		return parent::beforeValidate();
	}

}