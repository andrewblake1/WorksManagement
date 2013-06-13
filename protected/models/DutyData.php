<?php

/**
 * This is the model class for table "tbl_duty_data".
 *
 * The followings are the available columns in table 'tbl_duty_data':
 * @property string $id
 * @property string $planning_id
 * @property integer $duty_step_id
 * @property string $level
 * @property integer $responsible
 * @property string $updated
 * @property string $custom_value_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property CustomValue $customValue
 * @property User $updatedBy
 * @property Planning $planning
 * @property DutyStep $level0
 * @property User $responsible0
 * @property DutyStep $dutyStep
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
		return array_merge(parent::rules(), array(
			array('planning_id, duty_step_id, level', 'required'),
			array('duty_step_id, responsible', 'numerical', 'integerOnly'=>true),
			array('planning_id, level, custom_value_id', 'length', 'max'=>10),
			array('updated', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, planning_id, duty_step_id, level, updated, custom_value_id', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'duties' => array(self::HAS_MANY, 'Duty', 'duty_data_id'),
            'customValue' => array(self::BELONGS_TO, 'CustomValue', 'custom_value_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
            'level0' => array(self::BELONGS_TO, 'DutyStep', 'level'),
            'responsible0' => array(self::BELONGS_TO, 'User', 'responsible'),
            'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'planning_id' => 'Planning',
			'duty_step_id' => 'Duty',
			'level' => 'Level',
			'updated' => 'Updated',
			'custom_value_id' => 'Custom value',
		));
	}

	public function beforeSave()
	{
$t = $this->attributes;
		// if the updated attribute was null but is now being set
		if($this->updated !== NULL && $this->getOldAttributeValue('updated') == null)
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

	static function getDisplayAttr()
	{
		return array(
			'dutyStep->description',
		);
	}

}