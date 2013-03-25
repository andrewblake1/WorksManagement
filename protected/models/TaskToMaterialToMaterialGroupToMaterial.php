<?php

/**
 * This is the model class for table "task_to_material_to_material_group_to_material".
 *
 * The followings are the available columns in table 'task_to_material_to_material_group_to_material':
 * @property string $id
 * @property string $task_to_material_id
 * @property integer $material_id
 * @property integer $material_group_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property MaterialGroupToMaterial $materialGroup
 */
class TaskToMaterialToMaterialGroupToMaterial extends ActiveRecord
{
	// This model is here to provide ability to link a task_to_material record with a material group and enforce referencial
	// integrity such that once a task_to_material record is created that is a foreign key to this models table then the database
	// will ensure that the material_group_id does not change. Once a row is successfully created in here, there alterations to material_id
	// task_to_material will cascade to this models table only if the material_id exists in the material_group - otherwise the change will
	// be blocked by violation of another foreign key constraint from this models table to the material_group_to_material table.
}