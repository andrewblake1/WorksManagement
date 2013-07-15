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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('duty_step_id, mode_id', 'required'),
			array('duty_step_id, mode_id', 'numerical', 'integerOnly'=>true),
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
			'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
			'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'searchMode' => 'Mode',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'mode.description AS searchMode',
		);

		$criteria->compare('mode.description',$this->searchMode,true);
		$criteria->compare('t.duty_step_id', $this->duty_step_id);

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

}

?>