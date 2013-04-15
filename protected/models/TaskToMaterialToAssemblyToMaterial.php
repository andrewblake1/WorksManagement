<?php

/**
 * This is the model class for table "task_to_material_to_assembly_to_material".
 *
 * The followings are the available columns in table 'task_to_material_to_assembly_to_material':
 * @property integer $id
 * @property string $task_to_material_id
 * @property integer $assembly_to_material_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial $assemblyToMaterial
 * @property Staff $staff
 * @property TaskToMaterial $taskToMaterial
 */
class TaskToMaterialToAssemblyToMaterial extends ActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_to_material_id, assembly_to_material_id, staff_id', 'required'),
			array('assembly_to_material_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_to_material_id', 'length', 'max'=>10),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'assemblyToMaterial' => array(self::BELONGS_TO, 'AssemblyToMaterial', 'assembly_to_material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
		);
	}

}