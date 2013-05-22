 <?php

/**
 * This is the model class for table "tbl_duty_step_dependency".
 *
 * The followings are the available columns in table 'tbl_duty_step_dependency':
 * @property string $id
 * @property integer $parent_duty_step_id
 * @property integer $child_duty_step_id
 * @property string $duty_type_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyData[] $dutyDatas
 * @property DutyData[] $dutyDatas1
 * @property DutyStep $parentDutyStep
 * @property DutyStep $childDutyStep
 * @property User $updatedBy
 * @property DutyType $dutyType
 */
class DutyStepDependency extends ActiveRecord
{
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
		return array_merge(parent::rules(), array(
            array('id, parent_duty_step_id, child_duty_step_id, duty_type_id, updated_by', 'required'),
            array('parent_duty_step_id, child_duty_step_id, deleted, updated_by', 'numerical', 'integerOnly'=>true),
            array('id, duty_type_id', 'length', 'max'=>10),
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
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'duty_step_id'),
            'dutyDatas1' => array(self::HAS_MANY, 'DutyData', 'duty_step_dependency_id'),
            'parentDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'parent_duty_step_id'),
            'childDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'child_duty_step_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'parent_duty_step_id' => 'Parent Duty Step',
            'child_duty_step_id' => 'Child Duty Step',
            'duty_type_id' => 'Duty Type',
            'deleted' => 'Deleted',
            'updated_by' => 'Updated By',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('parent_duty_step_id',$this->parent_duty_step_id);
        $criteria->compare('child_duty_step_id',$this->child_duty_step_id);
        $criteria->compare('duty_type_id',$this->duty_type_id,true);
        $criteria->compare('deleted',$this->deleted);
        $criteria->compare('updated_by',$this->updated_by);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
} 