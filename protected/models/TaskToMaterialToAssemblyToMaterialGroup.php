<?php

/**
 * This is the model class for table "tbl_task_to_material_to_assembly_to_material_group".
 *
 * The followings are the available columns in table 'tbl_task_to_material_to_assembly_to_material_group':
 * @property string $id
 * @property string $task_id
 * @property string $task_to_material_id
 * @property integer $material_id
 * @property integer $material_group_id
 * @property integer $assembly_to_material_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property User $updatedBy
 * @property AssemblyToMaterialGroup $assemblyToMaterialGroup
 * @property MaterialGroupToMaterial $materialGroup
 * @property TaskToMaterial $task
 */
class TaskToMaterialToAssemblyToMaterialGroup extends ActiveRecord
{
	use RangeActiveRecordTrait;

	public $quantity;
	public $task_to_assembly_id;
	
	public $searchMaterialGroup;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';
	/**
	 * @var string label for tab and breadcrumbs when creating
	 */
	static $createLabel = 'Select material';
	/**
	 * @var string label on button in create view
	 */
	static $createButtonText = 'Save';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('task_to_material_id')), array(
			array('task_to_assembly_id, quantity', 'required'),
			array('task_to_assembly_id, quantity', 'numerical', 'integerOnly'=>true),
		));
	}

	public function setCustomValidators()
	{
		$rangeModel = AssemblyToMaterialGroup::model()->findByPk($this->assembly_to_material_group_id);
		
		$this->setCustomValidatorsFromSource($rangeModel);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'assemblyToMaterialGroup' => array(self::BELONGS_TO, 'AssemblyToMaterialGroup', 'assembly_to_material_group_id'),
            'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchMaterialGroup', $this->searchMaterialGroup, 'materialGroup.description', true);

		$criteria->with = array(
			'materialGroup',
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchMaterialGroup',
		);
	}

	public function assertFromParent($modelName = null) {
		
		// need to trick it here into using task to material model instead as this model not in navigation hierachy
		if(!empty($this->task_to_material_id))
		{
			Controller::setUpdateId($this->task_to_material_id, 'TaskToMaterial');
			$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
			return $taskToMaterial->assertFromParent('TaskToMaterial');
		}
		elseif(!empty($this->task_id))
		{
			Controller::setUpdateId($this->task_id, 'Task');
			$task = Task::model()->findByPk($this->task_id);
			return $task->assertFromParent('Task');
		}

		return parent::assertFromParent($modelName);
	}
	
	public function afterFind() {
		
		$task_to_material_id = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$this->quantity = $task_to_material_id->quantity;

		parent::afterFind();
	}
	
	public function updateSave(&$models = array()) {
		$saved = true;
		
		$taskToMaterial = $this->task_to_material_id
			? $this->taskToMaterial
			: new TaskToMaterial;

		$taskToMaterial->attributes = $_POST[__CLASS__];
		// filler - unused in this context but necassary in Material model
		$taskToMaterial->standard_id = 0;
		
		// if selection
		if($taskToMaterial->material_id)
		{
			$saved = $taskToMaterial->id
				? $taskToMaterial->updateSave($models)
				: $taskToMaterial->createSave($models);
			$this->task_to_material_id = $taskToMaterial->id;
		}
		elseif($taskToMaterial->id)	// existing row
		{
			$saved = $taskToMaterial->delete();
			$this->task_to_material_id = null;
		}
		
		if($saved)
		{
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}
	
	public function delete()
	{
		$return = parent::delete();

		$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_material WHERE id = :id');
		$command->bindParam(':id', $temp = $this->task_to_material_id);
		$command->execute();
		
		return $return;
	}

}