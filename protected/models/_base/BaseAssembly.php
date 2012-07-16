<?php

/**
 * This is the model base class for the table "assembly".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Assembly".
 *
 * Columns in table "assembly" available as properties of the model,
 * followed by relations of table "assembly" available as properties of the model.
 *
 * @property integer $id
 * @property integer $plan_id
 * @property integer $material_id
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $staff_id
 *
 * @property Plan $plan
 * @property Material $material
 * @property Staff $staff
 * @property Task[] $tasks
 */
abstract class BaseAssembly extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'assembly';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Assembly|Assemblies', $n);
	}

	public static function representingColumn() {
		return array(
			'id',
			'plan_id',
			'material_id',
		);
	}

	public function rules() {
		return array(
			array('plan_id, material_id, quantity, staff_id', 'required'),
			array('plan_id, material_id, quantity, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('deleted', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, plan_id, material_id, quantity, deleted, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'plan' => array(self::BELONGS_TO, 'Plan', 'plan_id'),
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'tasks' => array(self::MANY_MANY, 'Task', 'task_to_assembly(assembly_id, task_id)'),
		);
	}

	public function pivotModels() {
		return array(
			'tasks' => 'TaskToAssembly',
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'plan_id' => null,
			'material_id' => null,
			'quantity' => Yii::t('app', 'Quantity'),
			'deleted' => Yii::t('app', 'Deleted'),
			'staff_id' => null,
			'plan' => null,
			'material' => null,
			'staff' => null,
			'tasks' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('plan_id', $this->plan_id);
		$criteria->compare('material_id', $this->material_id);
		$criteria->compare('quantity', $this->quantity);
		$criteria->compare('deleted', $this->deleted);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}