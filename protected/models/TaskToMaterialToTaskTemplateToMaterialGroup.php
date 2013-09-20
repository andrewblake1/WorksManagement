<?php

/**
 * This is the model class for table "tbl_task_to_material_to_task_template_to_material_group".
 *
 * The followings are the available columns in table 'tbl_task_to_material_to_task_template_to_material_group':
 * @property string $id
 * @property string $task_id
 * @property string $task_to_material_id
 * @property integer $material_id
 * @property integer $material_group_id
 * @property integer $task_template_to_material_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property TaskTemplateToMaterialGroup $materialGroup
 * @property TaskTemplateToMaterialGroup $taskTemplateToMaterialGroup
 * @property TaskToMaterial $task
 */
class TaskToMaterialToTaskTemplateToMaterialGroup extends ActiveRecord
{
	use RangeActiveRecordTrait;

	public $quantity;
	
	public $searchMaterialGroup;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('task_to_material_id')), array(
			array('quantity', 'required'),
			array('quantity', 'numerical', 'integerOnly'=>true),
		));
	}

	public function setCustomValidators()
	{
		$this->setCustomValidatorsFromSource($this->taskTemplateToMaterialGroup);
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
            'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
            'taskTemplateToMaterialGroup' => array(self::BELONGS_TO, 'TaskTemplateToMaterialGroup', 'task_template_to_material_group_id'),
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
		if($task_to_material_id = TaskToMaterial::model()->findByPk($this->task_to_material_id))
		{
			$this->quantity = $task_to_material_id->quantity;
		}

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
			if($saved = $taskToMaterial->id
				? $taskToMaterial->updateSave($models)
				: $taskToMaterial->createSave($models))
			{
				$this->task_to_material_id = $taskToMaterial->id;
				$saved &= parent::updateSave($models);
			}
		}
		elseif($taskToMaterial->id)	// existing row
		{
			// NB: it is important that the task_to_material id of this is set to null in the database
			// prior to removing the below record otherwise a constraint will cause a failure
			$this->task_to_material_id = null;
			$saved &= parent::updateSave($models);

			// can't use models delete as will result in this being deleted also which is not desired
			// the delete operation required here should live $this but delete task_to_material
			$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_material WHERE id = :id');
			$command->bindParam(':id', $temp = $taskToMaterial->id);
			$command->execute();
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