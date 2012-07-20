<?php

/**
 * This is the model class for table "project_to_AuthAssignment_to_client_to_task_type_to_duty_type".
 *
 * The followings are the available columns in table 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type':
 * @property string $id
 * @property string $project_to_AuthAssignment_id
 * @property integer $client_to_task_type_to_duty_type_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property ProjectToAuthAssignment $projectToAuthAssignment
 * @property ClientToTaskTypeToDutyType $clientToTaskTypeToDutyType
 */
class ProjectToAuthAssignmentToClientToTaskTypeToDutyType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToAuthAssignmentToClientToTaskTypeToDutyType the static model class
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
		return 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_to_AuthAssignment_id, client_to_task_type_to_duty_type_id', 'required'),
			array('client_to_task_type_to_duty_type_id', 'numerical', 'integerOnly'=>true),
			array('project_to_AuthAssignment_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_to_AuthAssignment_id, client_to_task_type_to_duty_type_id', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id'),
			'projectToAuthAssignment' => array(self::BELONGS_TO, 'ProjectToAuthAssignment', 'project_to_AuthAssignment_id'),
			'clientToTaskTypeToDutyType' => array(self::BELONGS_TO, 'ClientToTaskTypeToDutyType', 'client_to_task_type_to_duty_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'project_to_AuthAssignment_id' => 'Project To Auth Assignment',
			'client_to_task_type_to_duty_type_id' => 'Client To Task Type To Duty Type',
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
		$criteria->compare('project_to_AuthAssignment_id',$this->project_to_AuthAssignment_id,true);
		$criteria->compare('client_to_task_type_to_duty_type_id',$this->client_to_task_type_to_duty_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}