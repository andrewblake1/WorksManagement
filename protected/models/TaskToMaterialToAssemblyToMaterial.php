<?php

/**
 * This is the model class for table "tbl_task_to_material_to_assembly_to_material".
 *
 * The followings are the available columns in table 'tbl_task_to_material_to_assembly_to_material':
 * @property integer $id
 * @property string $task_to_material_id
 * @property integer $assembly_to_material_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskToMaterial $taskToMaterial
 * @property AssemblyToMaterial $assemblyToMaterial
 * @property User $updatedBy
 */
class TaskToMaterialToAssemblyToMaterial extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
		return array(
            'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
            'assemblyToMaterial' => array(self::BELONGS_TO, 'AssemblyToMaterial', 'assembly_to_material_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

}