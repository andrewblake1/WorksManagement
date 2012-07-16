<?php

/**
 * This is the model base class for the table "task_to_generic_task_type".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "TaskToGenericTaskType".
 *
 * Columns in table "task_to_generic_task_type" available as properties of the model,
 * followed by relations of table "task_to_generic_task_type" available as properties of the model.
 *
 * @property string $task_id
 * @property integer $generic_task_type_client_to_task_type_client_id
 * @property integer $generic_task_type_client_to_task_type_task_type_id
 * @property string $generic_task_type_description
 * @property string $generic_id
 * @property integer $staff_id
 *
 * @property Task $task
 * @property GenericTaskType $genericTaskTypeClientToTaskTypeClient
 * @property GenericTaskType $genericTaskTypeClientToTaskTypeTaskType
 * @property GenericTaskType $genericTaskTypeDescription
 * @property Generic $generic
 * @property Staff $staff
 */
abstract class BaseTaskToGenericTaskType extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'task_to_generic_task_type';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'TaskToGenericTaskType|TaskToGenericTaskTypes', $n);
	}

	public static function representingColumn() {
		return 'generic_task_type_description';
	}

	public function rules() {
		return array(
			array('task_id, generic_task_type_client_to_task_type_client_id, generic_task_type_client_to_task_type_task_type_id, generic_task_type_description, generic_id, staff_id', 'required'),
			array('generic_task_type_client_to_task_type_client_id, generic_task_type_client_to_task_type_task_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, generic_id', 'length', 'max'=>10),
			array('generic_task_type_description', 'length', 'max'=>64),
			array('task_id, generic_task_type_client_to_task_type_client_id, generic_task_type_client_to_task_type_task_type_id, generic_task_type_description, generic_id, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'genericTaskTypeClientToTaskTypeClient' => array(self::BELONGS_TO, 'GenericTaskType', 'generic_task_type_client_to_task_type_client_id'),
			'genericTaskTypeClientToTaskTypeTaskType' => array(self::BELONGS_TO, 'GenericTaskType', 'generic_task_type_client_to_task_type_task_type_id'),
			'genericTaskTypeDescription' => array(self::BELONGS_TO, 'GenericTaskType', 'generic_task_type_description'),
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'task_id' => null,
			'generic_task_type_client_to_task_type_client_id' => null,
			'generic_task_type_client_to_task_type_task_type_id' => null,
			'generic_task_type_description' => null,
			'generic_id' => null,
			'staff_id' => null,
			'task' => null,
			'genericTaskTypeClientToTaskTypeClient' => null,
			'genericTaskTypeClientToTaskTypeTaskType' => null,
			'genericTaskTypeDescription' => null,
			'generic' => null,
			'staff' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('task_id', $this->task_id);
		$criteria->compare('generic_task_type_client_to_task_type_client_id', $this->generic_task_type_client_to_task_type_client_id);
		$criteria->compare('generic_task_type_client_to_task_type_task_type_id', $this->generic_task_type_client_to_task_type_task_type_id);
		$criteria->compare('generic_task_type_description', $this->generic_task_type_description);
		$criteria->compare('generic_id', $this->generic_id);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}