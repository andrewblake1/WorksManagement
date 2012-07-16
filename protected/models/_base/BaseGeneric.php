<?php

/**
 * This is the model base class for the table "generic".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Generic".
 *
 * Columns in table "generic" available as properties of the model,
 * followed by relations of table "generic" available as properties of the model.
 *
 * @property string $id
 * @property integer $type_int
 * @property double $type_float
 * @property string $type_time
 * @property string $type_date
 * @property string $type_text
 * @property integer $staff_id
 *
 * @property Staff $staff
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
abstract class BaseGeneric extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'generic';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Generic|Generics', $n);
	}

	public static function representingColumn() {
		return 'type_time';
	}

	public function rules() {
		return array(
			array('staff_id', 'required'),
			array('type_int, staff_id', 'numerical', 'integerOnly'=>true),
			array('type_float', 'numerical'),
			array('type_text', 'length', 'max'=>255),
			array('type_time, type_date', 'safe'),
			array('type_int, type_float, type_time, type_date, type_text', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, type_int, type_float, type_time, type_date, type_text, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'generic_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'type_int' => Yii::t('app', 'Type Int'),
			'type_float' => Yii::t('app', 'Type Float'),
			'type_time' => Yii::t('app', 'Type Time'),
			'type_date' => Yii::t('app', 'Type Date'),
			'type_text' => Yii::t('app', 'Type Text'),
			'staff_id' => null,
			'staff' => null,
			'taskToGenericTaskTypes' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('type_int', $this->type_int);
		$criteria->compare('type_float', $this->type_float);
		$criteria->compare('type_time', $this->type_time, true);
		$criteria->compare('type_date', $this->type_date, true);
		$criteria->compare('type_text', $this->type_text, true);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}