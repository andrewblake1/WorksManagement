<?php

/**
 * This is the model class for table "duty_data".
 *
 * The followings are the available columns in table 'duty_data':
 * @property string $id
 * @property string $planning_id
 * @property integer $duty_type_id
 * @property string $level
 * @property string $updated
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property Duty[] $duties1
 * @property Generic $generic
 * @property Staff $staff
 * @property Planning $planning
 * @property DutyType $level0
 * @property DutyType $dutyType
 */
class DutyData extends ActiveRecord
{

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('planning_id, duty_type_id, level', 'required'),
			array('duty_type_id', 'numerical', 'integerOnly'=>true),
			array('planning_id, level, generic_id', 'length', 'max'=>10),
			array('updated', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, planning_id, duty_type_id, level, updated, generic_id', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'duty_type_id'),
			'duties1' => array(self::HAS_MANY, 'Duty', 'duty_data_id'),
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
			'level0' => array(self::BELONGS_TO, 'DutyType', 'level'),
			'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'planning_id' => 'Planning',
			'duty_type_id' => 'Duty type',
			'level' => 'Level',
			'updated' => 'Updated',
			'generic_id' => 'Generic',
		));
	}

	public function beforeSave()
	{
		// if the updated attribute was null but is now being set
		if($this->updated == 1 && $this->getOldAttributeValue('updated') == null)
		{
			// set to current datetime
			$this->updated = date('Y-m-d H:i:s');
		}
		// system admin clear
		elseif(empty($this->updated) && Yii::app()->user->checkAccess('system admin'))
		{
			// clear
			$this->updated = null;
		}
		
		return parent::beforeSave();
	}

}