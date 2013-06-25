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
 * @property string $custom_value
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property User $updatedBy
 * @property Planning $planning
 * @property Planning $level0
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
			array('planning_id, level', 'length', 'max'=>10),
            array('custom_value', 'length', 'max'=>255),
			array('updated', 'safe'),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
            'level0' => array(self::BELONGS_TO, 'Planning', 'level'),
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
			'updated' => 'Completed',
			'custom_value' => 'Custom value',
		));
	}

	public function beforeSave()
	{
		// if the updated attribute was null but is now being set
		if(!empty($this->updated) && $this->getOldAttributeValue('updated') == null)
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