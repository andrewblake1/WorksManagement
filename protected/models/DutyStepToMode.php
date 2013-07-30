<?php

/**
 * This is the model class for table "tbl_duty_step_to_mode".
 *
 * The followings are the available columns in table 'tbl_duty_step_to_mode':
 * @property string $id
 * @property integer $duty_step_id
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStep $dutyStep
 * @property Mode $mode
 * @property User $updatedBy
 */
class DutyStepToMode extends ActiveRecord
{
	public $searchMode;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
			'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->compareAs(searchMode, $this->searchMode, 'mode.description', true);

		$criteria->with = array(
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchMode';

		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchMode',
		);
	}
 
}

?>