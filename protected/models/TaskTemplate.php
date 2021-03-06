<?php

/**
 * This is the model class for table "tbl_task_template".
 *
 * The followings are the available columns in table 'tbl_task_template':
 * @property integer $id
 * @property string $description
 * @property integer $project_template_id
 * @property integer $client_id
 * @property string $unit_price
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomFieldTaskCategory[] $customFieldTaskCategories
 * @property Task[] $tasks
 * @property User $updatedBy
 * @property ProjectTemplate $projectTemplate
 * @property ProjectTemplate $client
 * @property TaskTemplateToAction[] $taskTemplateToActions
 * @property TaskTemplateToAction[] $taskTemplateToActions1
 * @property TaskTemplateToAction[] $taskTemplateToActions2
 * @property TaskTemplateToAssembly[] $taskTemplateToAssemblies
 * @property TaskTemplateToAssemblyGroup[] $taskTemplateToAssemblyGroups
 * @property TaskTemplateToCustomField[] $taskTemplateToCustomFields
 * @property TaskTemplateToLabourResource[] $taskTemplateToLabourResources
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskTemplateToMaterialGroup[] $taskTemplateToMaterialGroups
 * @property TaskTemplateToPlant[] $taskTemplateToPlants
 */
class TaskTemplate extends ActiveRecord
{
	public $toolTipAttribute = 'comment';

	public function scopeCrew($crew_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('crew.id',$crew_id);
		$criteria->join='
			JOIN tbl_project_template projectTemplate ON t.project_template_id = projectTemplate.id AND projectTemplate.deleted = 0
			JOIN tbl_project_type projectType ON projectTemplate.id = projectType.project_template_id AND projectType.deleted = 0
			JOIN tbl_project project ON projectType.id = project.project_type_id
			JOIN tbl_day `day` ON project.id = `day`.project_id
			JOIN tbl_crew `crew` ON day.id = crew.day_id
		';

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
            'customFieldTaskCategories' => array(self::HAS_MANY, 'CustomFieldTaskCategory', 'task_template_id'),
            'tasks' => array(self::HAS_MANY, 'Task', 'task_template_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'client' => array(self::BELONGS_TO, 'ProjectTemplate', 'client_id'),
            'taskTemplateToActions' => array(self::HAS_MANY, 'TaskTemplateToAction', 'client_id'),
            'taskTemplateToActions1' => array(self::HAS_MANY, 'TaskTemplateToAction', 'task_template_id'),
            'taskTemplateToActions2' => array(self::HAS_MANY, 'TaskTemplateToAction', 'project_template_id'),
            'taskTemplateToActionToLabourResources' => array(self::HAS_MANY, 'TaskTemplateToActionToLabourResource', 'task_template_id'),
            'taskTemplateToActionToPlants' => array(self::HAS_MANY, 'TaskTemplateToActionToPlant', 'task_template_id'),
            'taskTemplateToAssemblies' => array(self::HAS_MANY, 'TaskTemplateToAssembly', 'task_template_id'),
            'taskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskTemplateToAssemblyGroup', 'task_template_id'),
            'taskTemplateToCustomFields' => array(self::HAS_MANY, 'TaskTemplateToCustomField', 'task_template_id'),
            'taskTemplateToLabourResources' => array(self::HAS_MANY, 'TaskTemplateToLabourResource', 'task_template_id'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'task_template_id'),
            'taskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskTemplateToMaterialGroup', 'task_template_id'),
            'taskTemplateToPlants' => array(self::HAS_MANY, 'TaskTemplateToPlant', 'task_template_id'),
        );
    }

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'alias';
		$columns[] = 'comment';
		$columns[] = 'unit_price';
  		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
		$columns[] = 'quantity_tooltip';
 		
		return $columns;
	}

	public function beforeValidate()
	{
		$projectTemplate = ProjectTemplate::model()->findByPk($this->project_template_id);
		$this->client_id = $projectTemplate->client_id;
		
		return parent::beforeValidate();
	}

	public static function getDisplayAttr()
	{
		return array(
			't.alias',
			't.description',
		);
	}
 
}

?>