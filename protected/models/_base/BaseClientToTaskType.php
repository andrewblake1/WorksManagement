<?php

/**
 * This is the model base class for the table "client_to_task_type".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "ClientToTaskType".
 *
 * Columns in table "client_to_task_type" available as properties of the model,
 * followed by relations of table "client_to_task_type" available as properties of the model.
 *
 * @property integer $client_id
 * @property integer $task_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes1
 * @property GenericTaskType[] $genericTaskTypes
 * @property GenericTaskType[] $genericTaskTypes1
 * @property Task[] $tasks
 * @property Task[] $tasks1
 */
abstract class BaseClientToTaskType extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'client_to_task_type';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'ClientToTaskType|ClientToTaskTypes', $n);
	}

	public static function representingColumn() {
		return array(
			'client_id',
			'task_type_id',
		);
	}

	public function rules() {
		return array(
			array('client_id, task_type_id, staff_id', 'required'),
			array('client_id, task_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('deleted', 'default', 'setOnEmpty' => true, 'value' => null),
			array('client_id, task_type_id, deleted, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'clientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'client_to_task_type_client_id'),
			'clientToTaskTypeToDutyTypes1' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'client_to_task_type_task_type_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'client_to_task_type_client_id'),
			'genericTaskTypes1' => array(self::HAS_MANY, 'GenericTaskType', 'client_to_task_type_task_type_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'client_to_task_type_client_id'),
			'tasks1' => array(self::HAS_MANY, 'Task', 'client_to_task_type_task_type_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'client_id' => null,
			'task_type_id' => null,
			'deleted' => Yii::t('app', 'Deleted'),
			'staff_id' => null,
			'clientToTaskTypeToDutyTypes' => null,
			'clientToTaskTypeToDutyTypes1' => null,
			'genericTaskTypes' => null,
			'genericTaskTypes1' => null,
			'tasks' => null,
			'tasks1' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('client_id', $this->client_id);
		$criteria->compare('task_type_id', $this->task_type_id);
		$criteria->compare('deleted', $this->deleted);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}