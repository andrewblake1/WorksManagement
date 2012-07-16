<?php

/**
 * This is the model base class for the table "project".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Project".
 *
 * Columns in table "project" available as properties of the model,
 * followed by relations of table "project" available as properties of the model.
 *
 * @property string $id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $client_id
 * @property integer $staff_id
 *
 * @property Client $client
 * @property Staff $staff
 * @property AuthAssignment[] $authAssignments
 * @property GenericProjectType[] $genericProjectTypes
 * @property Task[] $tasks
 */
abstract class BaseProject extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'project';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Project|Projects', $n);
	}

	public static function representingColumn() {
		return 'travel_time_1_way';
	}

	public function rules() {
		return array(
			array('client_id, staff_id', 'required'),
			array('client_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('travel_time_1_way, critical_completion, planned', 'safe'),
			array('travel_time_1_way, critical_completion, planned', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, travel_time_1_way, critical_completion, planned, client_id, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'authAssignments' => array(self::MANY_MANY, 'AuthAssignment', 'project_to_AuthAssignment(project_id, AuthAssignment_id)'),
			'genericProjectTypes' => array(self::MANY_MANY, 'GenericProjectType', 'project_to_generic_project_type(project_id, generic_project_type_id)'),
			'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
		);
	}

	public function pivotModels() {
		return array(
			'authAssignments' => 'ProjectToAuthAssignment',
			'genericProjectTypes' => 'ProjectToGenericProjectType',
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'travel_time_1_way' => Yii::t('app', 'Travel Time 1 Way'),
			'critical_completion' => Yii::t('app', 'Critical Completion'),
			'planned' => Yii::t('app', 'Planned'),
			'client_id' => null,
			'staff_id' => null,
			'client' => null,
			'staff' => null,
			'authAssignments' => null,
			'genericProjectTypes' => null,
			'tasks' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('travel_time_1_way', $this->travel_time_1_way, true);
		$criteria->compare('critical_completion', $this->critical_completion, true);
		$criteria->compare('planned', $this->planned, true);
		$criteria->compare('client_id', $this->client_id);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}