<?php

/**
 * This is the model class for table "AuthAssignment".
 *
 * The followings are the available columns in table 'AuthAssignment':
 * @property integer $id
 * @property string $itemname
 * @property integer $userid
 * @property string $bizrule
 * @property string $data
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $user
 * @property Staff $staff
 * @property AuthItem $itemname0
 * @property ProjectToAuthAssignment[] $projectToAuthAssignments
 */
class AuthAssignment extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuthAssignment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'AuthAssignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('itemname, userid, staff_id', 'required'),
			array('userid, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('itemname', 'length', 'max'=>64),
			array('bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, itemname, userid, bizrule, data, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Staff', 'userid'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'itemname0' => array(self::BELONGS_TO, 'AuthItem', 'itemname'),
			'projectToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthAssignment', 'AuthAssignment_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'itemname' => 'Itemname',
			'userid' => 'Userid',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('itemname',$this->itemname,true);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('bizrule',$this->bizrule,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}