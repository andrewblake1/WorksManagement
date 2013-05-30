<?php

/**
 * This is the model class for table "tbl_level".
 *
 * The followings are the available columns in table 'tbl_level':
 * @property string $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property CrewLevel $crewLevel
 * @property DayLevel $dayLevel
 * @property DutyStep[] $dutySteps
 * @property ProjectLevel $projectLevel
 * @property TaskLevel $taskLevel
 */
class Level extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'crewLevel' => array(self::HAS_ONE, 'CrewLevel', 'level'),
			'dayLevel' => array(self::HAS_ONE, 'DayLevel', 'level'),
			'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'level'),
			'projectLevel' => array(self::HAS_ONE, 'ProjectLevel', 'level'),
			'taskLevel' => array(self::HAS_ONE, 'TaskLevel', 'level'),
		);
	}

}