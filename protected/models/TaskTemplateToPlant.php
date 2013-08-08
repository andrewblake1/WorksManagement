<?php

/**
 * This is the model class for table "tbl_task_template_to_plant".
 *
 * The followings are the available columns in table 'tbl_task_template_to_plant':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $plant_id
 * @property integer $plant_to_supplier_id
 * @property string $level
 * @property integer $mode_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property PlantToSupplier $plant
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level0
 * @property PlantToSupplier $plantToSupplier
 * @property TaskTemplateToPlantCapability[] $taskTemplateToPlantCapabilities
 */
class TaskTemplateToPlant extends ActiveRecord
{
	static $niceNamePlural = 'Plant';

	public $searchPlant;
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
            'plant' => array(self::BELONGS_TO, 'Plant', 'plant_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_to_supplier_id'),
            'taskTemplateToPlantCapabilities' => array(self::HAS_MANY, 'TaskTemplateToPlantCapability', 'task_template_to_plant_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchPlant', $this->searchPlant, 'plant.description', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);
		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);

		// with
		$criteria->with = array(
			'plant',
			'level0',
			'mode',
			'plantToSupplier.supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchPlant';
  		$columns[] = 'searchMode';
 		$columns[] = 'searchLevel';
 		$columns[] = 'searchSupplier';
		$columns[] = 'quantity';
		$columns[] = 'duration';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchPlant',
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