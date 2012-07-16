<?php

/**
 * This is the model base class for the table "crew".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Crew".
 *
 * Columns in table "crew" available as properties of the model,
 * followed by relations of table "crew" available as properties of the model.
 *
 * @property string $id
 * @property string $preferred_date
 * @property string $earliest_date
 * @property string $date_scheduled
 * @property integer $in_charge
 * @property integer $staff_id
 *
 * @property Staff $inCharge
 * @property Staff $staff
 * @property Task[] $tasks
 */
abstract class BaseCrew extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'crew';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Crew|Crews', $n);
	}

	public static function representingColumn() {
		return 'preferred_date';
	}

	public function rules() {
		return array(
			array('in_charge, staff_id', 'required'),
			array('in_charge, staff_id', 'numerical', 'integerOnly'=>true),
			array('preferred_date, earliest_date, date_scheduled', 'safe'),
			array('preferred_date, earliest_date, date_scheduled', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, preferred_date, earliest_date, date_scheduled, in_charge, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'crew_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'preferred_date' => Yii::t('app', 'Preferred Date'),
			'earliest_date' => Yii::t('app', 'Earliest Date'),
			'date_scheduled' => Yii::t('app', 'Date Scheduled'),
			'in_charge' => null,
			'staff_id' => null,
			'inCharge' => null,
			'staff' => null,
			'tasks' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('preferred_date', $this->preferred_date, true);
		$criteria->compare('earliest_date', $this->earliest_date, true);
		$criteria->compare('date_scheduled', $this->date_scheduled, true);
		$criteria->compare('in_charge', $this->in_charge);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}