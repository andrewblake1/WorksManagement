<?php

/**
 * This is the model base class for the table "generic_type".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "GenericType".
 *
 * Columns in table "generic_type" available as properties of the model,
 * followed by relations of table "generic_type" available as properties of the model.
 *
 * @property integer $id
 * @property string $label
 * @property string $mandatory
 * @property integer $allow_new
 * @property string $validation_type_id
 * @property string $data_type
 * @property integer $staff_id
 *
 * @property DutyType[] $dutyTypes
 * @property GenericProjectType[] $genericProjectTypes
 * @property GenericTaskType[] $genericTaskTypes
 * @property Staff $staff
 */
abstract class BaseGenericType extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'generic_type';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'GenericType|GenericTypes', $n);
	}

	public static function representingColumn() {
		return 'label';
	}

	public function rules() {
		return array(
			array('label, mandatory, validation_type_id, data_type, staff_id', 'required'),
			array('allow_new, staff_id', 'numerical', 'integerOnly'=>true),
			array('label, mandatory', 'length', 'max'=>64),
			array('validation_type_id', 'length', 'max'=>10),
			array('data_type', 'length', 'max'=>5),
			array('allow_new', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, label, mandatory, allow_new, validation_type_id, data_type, staff_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'dutyTypes' => array(self::HAS_MANY, 'DutyType', 'generic_type_id'),
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'generic_type_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'generic_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'label' => Yii::t('app', 'Label'),
			'mandatory' => Yii::t('app', 'Mandatory'),
			'allow_new' => Yii::t('app', 'Allow New'),
			'validation_type_id' => Yii::t('app', 'Validation Type'),
			'data_type' => Yii::t('app', 'Data Type'),
			'staff_id' => null,
			'dutyTypes' => null,
			'genericProjectTypes' => null,
			'genericTaskTypes' => null,
			'staff' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('label', $this->label, true);
		$criteria->compare('mandatory', $this->mandatory, true);
		$criteria->compare('allow_new', $this->allow_new);
		$criteria->compare('validation_type_id', $this->validation_type_id, true);
		$criteria->compare('data_type', $this->data_type, true);
		$criteria->compare('staff_id', $this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}