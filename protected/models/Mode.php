<?php

/**
 * This is the model class for table "tbl_mode".
 *
 * The followings are the available columns in table 'tbl_mode':
 * @property integer $id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepToMode[] $dutyStepToModes
 * @property User $updatedBy
 * @property HumanResourceDataToMode[] $humanResourceDataToModes
 * @property Task[] $tasks
 */
class Mode extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'dutyStepToModes' => array(self::HAS_MANY, 'DutyStepToMode', 'mode_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'resourceDataToModes' => array(self::HAS_MANY, 'HumanResourceDataToMode', 'mode_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'mode_id'),
		);
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		
		return $columns;
	}

}

?>