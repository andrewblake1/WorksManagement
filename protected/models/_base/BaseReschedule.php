<?php

/**
 * This is the model base class for the table "reschedule".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Reschedule".
 *
 * Columns in table "reschedule" available as properties of the model,
 * followed by relations of table "reschedule" available as properties of the model.
 *
 * @property string $task_old
 * @property string $task_new
 * @property integer $staff_id
 *
 * @property Task $taskOld
 * @property Task $taskNew
 * @property Staff $staff
 */
abstract class BaseReschedule extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'reschedule';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Reschedule|Reschedules', $n);
	}

	public static function representingColumn() {
		return array(
			'task_old',
			'task_new',
		);
	}

	public function rules() {
		return array(
			array('task_old, task_new, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('task_old, task_new', 'length', 'max'=>10),
			array('task_old, task_new, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'taskOld' => array(self::BELONGS_TO, 'Task', 'task_old'),
			'taskNew' => array(self::BELONGS_TO, 'Task', 'task_new'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'task_old' => null,
			'task_new' => null,
			'staff_id' => null,
			'taskOld' => null,
			'taskNew' => null,
			'staff' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('task_old', $this->task_old);
		$criteria->compare('task_new', $this->task_new);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}